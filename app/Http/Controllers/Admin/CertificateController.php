<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CertificateController extends Controller
{
    use ScopesToCurrentUser;

    public function index()
    {
        $certificates = Certificate::with(['user', 'course'])
            ->where(function ($q) {
                $q->whereIn('course_id', $this->ownedCourseIds())
                    ->orWhereHas('user', fn ($u) => $u->where('created_by', $this->currentUserId()));
            })
            ->latest()
            ->paginate(20);
        $templates = CertificateTemplate::all();

        return view('admin.certificates.index', compact('certificates', 'templates'));
    }

    public function templates()
    {
        $templates = CertificateTemplate::latest()->paginate(15);

        return view('admin.certificates.templates', compact('templates'));
    }

    public function storeTemplate(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'html_content' => ['required', 'string'],
            'is_default' => ['boolean'],
        ]);

        if ($request->boolean('is_default')) {
            CertificateTemplate::where('is_default', true)->update(['is_default' => false]);
        }

        CertificateTemplate::create($validated);

        return back()->with('success', 'Template created.');
    }

    public function issue(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', Rule::in($this->visibleLearnersQuery()->pluck('id'))],
            'course_id' => ['nullable', Rule::in($this->ownedCourseIds())],
            'certificate_template_id' => ['nullable', 'exists:certificate_templates,id'],
        ]);

        $cert = Certificate::create(array_merge($validated, ['issued_at' => now()]));
        ActivityLogger::log('certificate_issued', "Certificate {$cert->certificate_number} issued", $cert);

        return back()->with('success', 'Certificate issued.');
    }

    public function verify(Request $request)
    {
        $certificate = null;

        if ($request->filled('number')) {
            $certificate = Certificate::with(['user', 'course'])
                ->where('certificate_number', $request->number)
                ->where(function ($q) {
                    $q->whereIn('course_id', $this->ownedCourseIds())
                        ->orWhereHas('user', fn ($u) => $u->where('created_by', $this->currentUserId()));
                })
                ->first();
        }

        return view('certificates.verify', compact('certificate'));
    }
}
