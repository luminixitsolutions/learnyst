<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\PlacementApplication;
use App\Models\PlacementCompany;
use App\Models\PlacementJob;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PlacementController extends Controller
{
    use ScopesToCurrentUser;

    public function companies()
    {
        $companies = $this->owned(PlacementCompany::query())->withCount('jobs')->latest()->paginate(20);

        return view('admin.placements.companies', compact('companies'));
    }

    public function storeCompany(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'industry' => ['nullable', 'string', 'max:80'],
            'website' => ['nullable', 'url'],
            'contact_name' => ['nullable', 'string', 'max:120'],
            'contact_email' => ['nullable', 'email'],
            'contact_phone' => ['nullable', 'string', 'max:40'],
            'about' => ['nullable', 'string'],
        ]);
        $validated['created_by'] = Auth::id();
        $company = PlacementCompany::create($validated);
        ActivityLogger::log('placement_company_created', "Placement company {$company->name}", $company);

        return back()->with('success', 'Recruiter/company profile created (separate from LMS tenants).');
    }

    public function jobs()
    {
        $jobs = $this->owned(PlacementJob::query())->with('company')->withCount('applications')->latest()->paginate(20);
        $companies = $this->owned(PlacementCompany::query())->where('is_active', true)->orderBy('name')->get();

        return view('admin.placements.jobs', compact('jobs', 'companies'));
    }

    public function storeJob(Request $request)
    {
        $validated = $request->validate([
            'placement_company_id' => ['required', Rule::in($this->owned(PlacementCompany::query())->pluck('id')->all())],
            'title' => ['required', 'string', 'max:180'],
            'type' => ['required', 'in:job,internship'],
            'location' => ['nullable', 'string', 'max:120'],
            'employment_type' => ['nullable', 'string', 'max:40'],
            'stipend_or_salary' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'closes_at' => ['nullable', 'date'],
            'status' => ['nullable', 'in:draft,open,closed'],
        ]);
        $validated['created_by'] = Auth::id();
        $validated['status'] = $validated['status'] ?? 'open';
        PlacementJob::create($validated);

        return back()->with('success', 'Listing published.');
    }

    public function applications(Request $request)
    {
        $query = PlacementApplication::with(['job.company', 'user'])
            ->whereHas('job', fn ($q) => $q->where('created_by', Auth::id()));

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $applications = $query->latest()->paginate(30)->withQueryString();

        return view('admin.placements.applications', compact('applications'));
    }

    public function updateApplication(Request $request, PlacementApplication $application)
    {
        abort_unless($application->job && (int) $application->job->created_by === Auth::id(), 403);
        $validated = $request->validate([
            'status' => ['required', 'in:applied,shortlisted,interview,offered,rejected,hired'],
            'interview_at' => ['nullable', 'date'],
            'interview_mode' => ['nullable', 'string', 'max:40'],
            'interview_notes' => ['nullable', 'string'],
        ]);
        $application->update($validated);

        return back()->with('success', 'Application updated.');
    }

    public function reports()
    {
        $base = PlacementApplication::whereHas('job', fn ($q) => $q->where('created_by', Auth::id()));
        $byStatus = (clone $base)->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status');
        $byType = PlacementJob::where('created_by', Auth::id())
            ->select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');
        $hired = (clone $base)->where('status', 'hired')->count();

        return view('admin.placements.reports', compact('byStatus', 'byType', 'hired'));
    }
}
