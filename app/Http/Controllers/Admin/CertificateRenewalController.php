<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\CertificateLifecycleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CertificateRenewalController extends Controller
{
    use ScopesToCurrentUser;

    public function index(Request $request, CertificateLifecycleService $lifecycle)
    {
        $status = $request->query('status');

        $certificates = Certificate::with(['user', 'course', 'template'])
            ->where(function ($q) {
                $q->whereIn('course_id', $this->ownedCourseIds())
                    ->orWhereHas('user', fn ($u) => $u->where('created_by', $this->currentUserId()));
            })
            ->when($status, fn ($q) => $q->where('status', $status))
            ->whereNotNull('expires_at')
            ->latest('expires_at')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'expiring_soon' => Certificate::whereIn('course_id', $this->ownedCourseIds())->where('status', 'expiring_soon')->count(),
            'renewal_due' => Certificate::whereIn('course_id', $this->ownedCourseIds())->whereIn('status', ['renewal_due', 'expired'])->count(),
            'valid' => Certificate::whereIn('course_id', $this->ownedCourseIds())->where('status', 'valid')->whereNotNull('expires_at')->count(),
        ];

        return view('admin.certificates.renewals', compact('certificates', 'stats', 'lifecycle'));
    }

    public function updateTemplate(Request $request, CertificateTemplate $template)
    {
        $validated = $request->validate([
            'validity_years' => ['nullable', 'integer', 'min:0', 'max:50'],
            'validity_days' => ['nullable', 'integer', 'min:0', 'max:3650'],
            'renewal_price' => ['nullable', 'numeric', 'min:0'],
            'requires_renewal_assessment' => ['boolean'],
        ]);

        $template->update([
            'validity_years' => $validated['validity_years'] ?? null,
            'validity_days' => $validated['validity_days'] ?? null,
            'renewal_price' => $validated['renewal_price'] ?? null,
            'requires_renewal_assessment' => $request->boolean('requires_renewal_assessment'),
        ]);

        ActivityLogger::log('certificate_template_updated', "Renewal settings updated for template {$template->name}", $template);

        return back()->with('success', 'Template validity and renewal settings saved.');
    }

    public function bulkRenew(Request $request, CertificateLifecycleService $lifecycle)
    {
        $validated = $request->validate([
            'certificate_ids' => ['required', 'array', 'min:1'],
            'certificate_ids.*' => ['integer', Rule::exists('certificates', 'id')],
        ]);

        $scopedIds = Certificate::query()
            ->whereIn('id', $validated['certificate_ids'])
            ->where(function ($q) {
                $q->whereIn('course_id', $this->ownedCourseIds())
                    ->orWhereHas('user', fn ($u) => $u->where('created_by', $this->currentUserId()));
            })
            ->pluck('id');

        $renewed = 0;

        DB::transaction(function () use ($scopedIds, $lifecycle, &$renewed) {
            foreach ($scopedIds as $id) {
                $certificate = Certificate::with('template', 'course')->find($id);
                if (! $certificate || ! $lifecycle->isRenewable($certificate)) {
                    continue;
                }

                $lifecycle->renew($certificate);
                $renewed++;
            }
        });

        ActivityLogger::log('certificates_bulk_renewed', "Admin bulk renewed {$renewed} certificate(s)");

        return back()->with('success', "{$renewed} certificate(s) renewed.");
    }
}
