<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Company;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\ScheduledEvent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PlatformAcademicService
{
    public function companiesByOwner(): Collection
    {
        return Company::query()->get()->keyBy('owner_user_id');
    }

    public function ownerIdForCompany(int $companyId): ?int
    {
        return Company::where('id', $companyId)->value('owner_user_id');
    }

    public function resolveCompanyForOwner(?int $ownerId, ?Collection $byOwner = null): ?Company
    {
        if (! $ownerId) {
            return null;
        }

        $byOwner ??= $this->companiesByOwner();

        return $byOwner->get($ownerId);
    }

    public function resolveCompanyForCourse(Course $course, ?Collection $byOwner = null): ?Company
    {
        return $this->resolveCompanyForOwner($course->created_by, $byOwner);
    }

    public function resolveCompanyForEnrollment(CourseEnrollment $enrollment, ?Collection $byOwner = null): ?Company
    {
        $enrollment->loadMissing('course');

        if (! $enrollment->course) {
            return null;
        }

        return $this->resolveCompanyForCourse($enrollment->course, $byOwner);
    }

    public function resolveCompanyForCertificate(Certificate $certificate, ?Collection $byOwner = null): ?Company
    {
        $certificate->loadMissing('course');

        if ($certificate->course) {
            return $this->resolveCompanyForCourse($certificate->course, $byOwner);
        }

        $certificate->loadMissing('user');

        return $this->resolveCompanyForOwner($certificate->user?->created_by, $byOwner);
    }

    public function resolveCompanyForEvent(ScheduledEvent $event, ?Collection $byOwner = null): ?Company
    {
        $byOwner ??= $this->companiesByOwner();

        if ($event->created_by && $byOwner->has($event->created_by)) {
            return $byOwner->get($event->created_by);
        }

        $event->loadMissing('course');

        if ($event->course) {
            return $this->resolveCompanyForCourse($event->course, $byOwner);
        }

        return null;
    }

    public function scopeCoursesForCompany(Builder $query, int $companyId): Builder
    {
        $ownerId = $this->ownerIdForCompany($companyId);
        if (! $ownerId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('created_by', $ownerId);
    }

    public function scopeEnrollmentsForCompany(Builder $query, int $companyId): Builder
    {
        $ownerId = $this->ownerIdForCompany($companyId);
        if (! $ownerId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('course', fn (Builder $c) => $c->where('created_by', $ownerId));
    }

    public function scopeCertificatesForCompany(Builder $query, int $companyId): Builder
    {
        $ownerId = $this->ownerIdForCompany($companyId);
        if (! $ownerId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $q) use ($ownerId) {
            $q->whereHas('course', fn (Builder $c) => $c->where('created_by', $ownerId))
                ->orWhereHas('user', fn (Builder $u) => $u->where('created_by', $ownerId));
        });
    }

    public function scopeEventsForCompany(Builder $query, int $companyId): Builder
    {
        $ownerId = $this->ownerIdForCompany($companyId);
        if (! $ownerId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $q) use ($ownerId) {
            $q->where('created_by', $ownerId)
                ->orWhereHas('course', fn (Builder $c) => $c->where('created_by', $ownerId));
        });
    }

    /**
     * Paid order-item revenue for a course (sum of order_items.total on paid orders).
     */
    public function courseRevenue(int $courseId): float
    {
        return (float) \App\Models\OrderItem::query()
            ->where('course_id', $courseId)
            ->whereHas('order', fn (Builder $o) => $o->where('payment_status', 'paid'))
            ->sum('total');
    }
}
