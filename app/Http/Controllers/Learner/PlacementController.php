<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\PlacementApplication;
use App\Models\PlacementJob;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlacementController extends Controller
{
    public function __construct(protected GamificationService $gamification) {}

    public function index(Request $request)
    {
        $instituteId = $this->gamification->resolveInstituteId(Auth::user());
        $query = PlacementJob::with('company')
            ->where('status', 'open')
            ->when($instituteId, fn ($q) => $q->where('created_by', $instituteId));

        if ($request->get('type') === 'internship') {
            $query->where('type', 'internship');
        } elseif ($request->get('type') === 'job') {
            $query->where('type', 'job');
        }

        $jobs = $query->latest()->paginate(20)->withQueryString();
        $appliedIds = PlacementApplication::where('user_id', Auth::id())->pluck('placement_job_id');

        return view('learner.placements.index', compact('jobs', 'appliedIds'));
    }

    public function show(PlacementJob $job)
    {
        abort_unless($job->status === 'open', 404);
        $job->load('company');
        $application = PlacementApplication::where('placement_job_id', $job->id)->where('user_id', Auth::id())->first();

        return view('learner.placements.show', compact('job', 'application'));
    }

    public function apply(Request $request, PlacementJob $job)
    {
        abort_unless($job->status === 'open', 404);
        $validated = $request->validate([
            'cover_letter' => ['nullable', 'string', 'max:5000'],
            'resume' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:4096'],
            'resume_summary' => ['nullable', 'string', 'max:2000'],
            'skills' => ['nullable', 'string', 'max:500'],
            'education' => ['nullable', 'string', 'max:500'],
        ]);

        $path = null;
        if ($request->hasFile('resume')) {
            $path = $request->file('resume')->store('placements/resumes', 'public');
        }

        PlacementApplication::updateOrCreate(
            ['placement_job_id' => $job->id, 'user_id' => Auth::id()],
            [
                'created_by' => $job->created_by,
                'status' => 'applied',
                'cover_letter' => $validated['cover_letter'] ?? null,
                'resume_path' => $path,
                'resume_data' => [
                    'summary' => $validated['resume_summary'] ?? null,
                    'skills' => $validated['skills'] ?? null,
                    'education' => $validated['education'] ?? null,
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                    'phone' => Auth::user()->phone,
                ],
            ]
        );

        return back()->with('success', 'Application submitted.');
    }

    public function myApplications()
    {
        $applications = PlacementApplication::with('job.company')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        return view('learner.placements.applications', compact('applications'));
    }

    public function resumeBuilder()
    {
        $user = Auth::user();

        return view('learner.placements.resume', compact('user'));
    }
}
