<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Batch;
use App\Models\Bundle;
use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

trait ScopesToCurrentUser
{
    protected function currentUserId(): int
    {
        return (int) Auth::id();
    }

    /**
     * Scope a query to records created by the logged-in company user
     * (and, for sub-admins, records assigned via SubAdminScope).
     */
    protected function owned(Builder|string $query, string $column = 'created_by'): Builder
    {
        if (is_string($query)) {
            $query = $query::query();
        }

        $user = Auth::user();
        $userId = $this->currentUserId();
        $modelClass = $query->getModel()::class;

        return $query->where(function (Builder $q) use ($user, $userId, $column, $modelClass) {
            $q->where($column, $userId);

            if ($user?->isSubAdmin()) {
                $scopedIds = $user->subAdminScopes()
                    ->where('scope_type', $modelClass)
                    ->pluck('scope_id');

                if ($scopedIds->isNotEmpty()) {
                    $q->orWhereIn('id', $scopedIds);
                }
            }
        });
    }

    protected function authorizeOwner(?Model $model, string $column = 'created_by'): void
    {
        abort_unless($model instanceof Model, 404);

        $user = Auth::user();
        $userId = $this->currentUserId();

        if ((int) $model->{$column} === $userId) {
            return;
        }

        if ($user?->isSubAdmin()) {
            $allowed = $user->subAdminScopes()
                ->where('scope_type', $model::class)
                ->where('scope_id', $model->getKey())
                ->exists();

            if ($allowed) {
                return;
            }
        }

        abort(403, 'You do not have access to this resource.');
    }

    protected function authorizeCourseOwner(Course $course): void
    {
        $this->authorizeOwner($course);
    }

    /** @return Collection<int, int> */
    protected function ownedCourseIds(): Collection
    {
        return $this->owned(Course::query())->pluck('id');
    }

    /** @return Collection<int, int> */
    protected function ownedBundleIds(): Collection
    {
        return $this->owned(Bundle::query())->pluck('id');
    }

    /** @return Collection<int, int> */
    protected function ownedBatchIds(): Collection
    {
        $courseIds = $this->ownedCourseIds();

        return Batch::whereIn('course_id', $courseIds)->pluck('id');
    }

    /** Users (learners / instructors / sub-admins) created by the current company user. */
    protected function ownedUsersQuery(?string $roleSlug = null): Builder
    {
        $query = $this->owned(User::query());

        if ($roleSlug) {
            $query->whereHas('role', fn (Builder $q) => $q->where('slug', $roleSlug));
        }

        return $query;
    }

    /**
     * Learners created by this admin, or enrolled in their courses/bundles/batches.
     */
    protected function visibleLearnersQuery(): Builder
    {
        $courseIds = $this->ownedCourseIds();
        $bundleIds = $this->ownedBundleIds();
        $batchIds = $this->ownedBatchIds();
        $userId = $this->currentUserId();

        return User::whereHas('role', fn (Builder $q) => $q->where('slug', 'learner'))
            ->where(function (Builder $q) use ($userId, $courseIds, $bundleIds, $batchIds) {
                $q->where('created_by', $userId)
                    ->orWhereHas('enrollments', function (Builder $e) use ($courseIds, $bundleIds, $batchIds) {
                        $e->where(function (Builder $inner) use ($courseIds, $bundleIds, $batchIds) {
                            $inner->whereIn('course_id', $courseIds)
                                ->orWhereIn('bundle_id', $bundleIds)
                                ->orWhereIn('batch_id', $batchIds);
                        });
                    });
            });
    }

    /** Orders that include items for courses owned by the current user. */
    protected function ownedOrdersQuery(): Builder
    {
        $courseIds = $this->ownedCourseIds();

        return Order::query()->whereHas(
            'items',
            fn (Builder $item) => $item->whereIn('course_id', $courseIds)
        );
    }

    protected function ownedEnrollmentsConstraint(Builder $query): Builder
    {
        $courseIds = $this->ownedCourseIds();
        $bundleIds = $this->ownedBundleIds();
        $batchIds = $this->ownedBatchIds();

        return $query->where(function (Builder $q) use ($courseIds, $bundleIds, $batchIds) {
            $q->whereIn('course_id', $courseIds)
                ->orWhereIn('bundle_id', $bundleIds)
                ->orWhereIn('batch_id', $batchIds);
        });
    }
}
