<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\User;
use App\Models\UserNotification;
use App\Mail\CertificateExpiryReminderMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class CertificateLifecycleService
{
    public const EXPIRING_SOON_DAYS = 90;

    public const REMINDER_THRESHOLDS = [90, 60, 30];

    public function computeExpiresAt(?CertificateTemplate $template, Carbon $issuedAt): ?Carbon
    {
        if (! $template) {
            return null;
        }

        $expiresAt = $issuedAt->copy();

        if ($template->validity_years) {
            $expiresAt->addYears((int) $template->validity_years);
        }

        if ($template->validity_days) {
            $expiresAt->addDays((int) $template->validity_days);
        }

        if (! $template->validity_years && ! $template->validity_days) {
            return null;
        }

        return $expiresAt;
    }

    public function resolveStatus(Certificate $certificate): string
    {
        if (! $certificate->expires_at) {
            return 'valid';
        }

        $now = now();
        $expiresAt = $certificate->expires_at;

        if ($expiresAt->isPast()) {
            return $certificate->template?->hasValidityPeriod() ? 'renewal_due' : 'expired';
        }

        if ($expiresAt->lte($now->copy()->addDays(self::EXPIRING_SOON_DAYS))) {
            return 'expiring_soon';
        }

        return 'valid';
    }

    public function applyLifecycle(Certificate $certificate, ?CertificateTemplate $template = null): Certificate
    {
        $template ??= $certificate->template;

        if (! $certificate->issued_at) {
            $certificate->issued_at = now();
        }

        if (! $certificate->expires_at && $template) {
            $certificate->expires_at = $this->computeExpiresAt($template, $certificate->issued_at);
        }

        $certificate->status = $this->resolveStatus($certificate);
        $certificate->save();

        return $certificate->fresh();
    }

    public function refreshAllStatuses(): int
    {
        $updated = 0;

        Certificate::query()
            ->whereNotNull('expires_at')
            ->chunkById(100, function ($certificates) use (&$updated) {
                foreach ($certificates as $certificate) {
                    $previous = $certificate->status;
                    $next = $this->resolveStatus($certificate);

                    if ($previous !== $next) {
                        $certificate->update(['status' => $next]);
                        $updated++;
                    }
                }
            });

        return $updated;
    }

    public function sendExpiryReminders(): int
    {
        $sent = 0;

        foreach (self::REMINDER_THRESHOLDS as $days) {
            $windowStart = now()->addDays($days)->startOfDay();
            $windowEnd = now()->addDays($days)->endOfDay();

            Certificate::query()
                ->with(['user', 'course', 'template'])
                ->whereNotNull('expires_at')
                ->whereBetween('expires_at', [$windowStart, $windowEnd])
                ->where(function ($q) use ($days) {
                    $q->whereNull('last_reminder_days')
                        ->orWhere('last_reminder_days', '>', $days);
                })
                ->whereIn('status', ['valid', 'expiring_soon', 'renewal_due'])
                ->chunkById(50, function ($certificates) use ($days, &$sent) {
                    foreach ($certificates as $certificate) {
                        if (! $certificate->user) {
                            continue;
                        }

                        $this->notifyExpiry($certificate, $days);
                        $certificate->update([
                            'last_reminder_at' => now(),
                            'last_reminder_days' => $days,
                        ]);
                        $sent++;
                    }
                });
        }

        return $sent;
    }

    public function notifyExpiry(Certificate $certificate, int $daysUntilExpiry): void
    {
        $user = $certificate->user;
        if (! $user) {
            return;
        }

        $courseTitle = $certificate->course?->title ?? 'your course';
        $expiresLabel = $certificate->expires_at?->format('M d, Y') ?? 'soon';
        $title = "Certificate expiring in {$daysUntilExpiry} days";
        $body = "Your certificate for {$courseTitle} expires on {$expiresLabel}. Renew now to keep it valid.";

        UserNotification::create([
            'user_id' => $user->id,
            'type' => 'certificate_expiry',
            'title' => $title,
            'body' => $body,
            'data' => [
                'certificate_id' => $certificate->id,
                'certificate_number' => $certificate->certificate_number,
                'days_until_expiry' => $daysUntilExpiry,
            ],
        ]);

        if ($user->email) {
            Mail::to($user->email)->send(new CertificateExpiryReminderMail($certificate, $daysUntilExpiry));
        }
    }

    public function renew(Certificate $certificate, ?CertificateTemplate $template = null): Certificate
    {
        $template ??= $certificate->template ?? ($certificate->course
            ? app(CertificateDesignService::class)->forCourse($certificate->course)
            : null);

        $issuedAt = now();
        $renewed = Certificate::create([
            'user_id' => $certificate->user_id,
            'course_id' => $certificate->course_id,
            'batch_id' => $certificate->batch_id,
            'certificate_template_id' => $template?->id ?? $certificate->certificate_template_id,
            'issued_at' => $issuedAt,
            'expires_at' => $this->computeExpiresAt($template, $issuedAt),
            'renewed_from_id' => $certificate->id,
            'renewal_count' => $certificate->renewal_count + 1,
            'status' => 'valid',
        ]);

        $certificate->update(['status' => 'expired']);

        return $this->applyLifecycle($renewed, $template);
    }

    public function isRenewable(Certificate $certificate): bool
    {
        if (! $certificate->expires_at) {
            return false;
        }

        return in_array($certificate->status, ['expiring_soon', 'expired', 'renewal_due'], true);
    }

    public function renewalPrice(Certificate $certificate): float
    {
        $template = $certificate->template;

        if ($template && $template->renewal_price !== null) {
            return (float) $template->renewal_price;
        }

        return 0.0;
    }

    public function statusLabel(string $status): string
    {
        return match ($status) {
            'valid' => 'Valid',
            'expiring_soon' => 'Expiring Soon',
            'expired' => 'Expired',
            'renewal_due' => 'Renewal Due',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    public function statusBadgeType(string $status): string
    {
        return match ($status) {
            'valid' => 'success',
            'expiring_soon' => 'warning',
            'expired', 'renewal_due' => 'danger',
            default => 'default',
        };
    }
}
