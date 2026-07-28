<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Services\ActivityLogger;
use App\Services\CertificateDesignService;
use App\Services\CertificateLifecycleService;
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
            ->get();
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
            'validity_years' => ['nullable', 'integer', 'min:0', 'max:50'],
            'validity_days' => ['nullable', 'integer', 'min:0', 'max:3650'],
            'renewal_price' => ['nullable', 'numeric', 'min:0'],
            'requires_renewal_assessment' => ['boolean'],
        ]);

        if ($request->boolean('is_default')) {
            CertificateTemplate::where('is_default', true)->update(['is_default' => false]);
        }

        CertificateTemplate::create([
            'name' => $validated['name'],
            'html_content' => $validated['html_content'],
            'is_default' => $request->boolean('is_default'),
            'validity_years' => $validated['validity_years'] ?? null,
            'validity_days' => $validated['validity_days'] ?? null,
            'renewal_price' => $validated['renewal_price'] ?? null,
            'requires_renewal_assessment' => $request->boolean('requires_renewal_assessment'),
        ]);

        return back()->with('success', 'Template created.');
    }

    public function issue(Request $request, CertificateLifecycleService $lifecycle)
    {
        $validated = $request->validate([
            'user_id' => ['required', Rule::in($this->visibleLearnersQuery()->pluck('id'))],
            'course_id' => ['nullable', Rule::in($this->ownedCourseIds())],
            'certificate_template_id' => ['nullable', 'exists:certificate_templates,id'],
        ]);

        $template = null;
        if (! empty($validated['certificate_template_id'])) {
            $template = CertificateTemplate::find($validated['certificate_template_id']);
        } elseif (! empty($validated['course_id'])) {
            $course = \App\Models\Course::find($validated['course_id']);
            $template = $course ? app(CertificateDesignService::class)->forCourse($course) : null;
        }

        $cert = Certificate::create(array_merge($validated, [
            'issued_at' => now(),
            'certificate_template_id' => $template?->id ?? $validated['certificate_template_id'] ?? null,
        ]));

        $lifecycle->applyLifecycle($cert, $template);

        ActivityLogger::log('certificate_issued', "Certificate {$cert->certificate_number} issued", $cert);

        return back()->with('success', 'Certificate issued.');
    }
}
