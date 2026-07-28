<?php

namespace App\Http\Controllers\Instructor\Concerns;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseLesson;
use App\Models\Discussion;
use App\Models\ScheduledEvent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

trait ScopesToInstructor
{
    protected function instructorId(): int
    {
        return (int) Auth::id();
    }

    protected function instituteOwnerId(): int
    {
        $user = Auth::user();

        return (int) ($user->created_by ?: $user->id);
    }

    /** @return Collection<int, int> */
    protected function assignedCourseIds(): Collection
    {
        return Auth::user()->courses()->pluck('courses.id');
    }

    protected function assertAssignedCourse(Course $course): void
    {
        if (! $this->assignedCourseIds()->contains($course->id)) {
            abort(403, 'You are not assigned to this course.');
        }
    }

    protected function assignedCoursesQuery(): Builder
    {
        return Course::query()->whereIn('id', $this->assignedCourseIds());
    }

    protected function assignedLessonsQuery(string $lessonType = null): Builder
    {
        $q = CourseLesson::query()
            ->whereHas('section', fn (Builder $s) => $s->whereIn('course_id', $this->assignedCourseIds()));

        if ($lessonType) {
            $q->where('lesson_type', $lessonType);
        }

        return $q;
    }

    protected function assignedEventsQuery(): Builder
    {
        $courseIds = $this->assignedCourseIds();

        return ScheduledEvent::query()
            ->where('type', 'class')
            ->where(function (Builder $q) use ($courseIds) {
                $q->where('instructor_id', $this->instructorId())
                    ->orWhereIn('course_id', $courseIds);
            });
    }

    protected function assignedDiscussionsQuery(): Builder
    {
        return Discussion::query()->where(function (Builder $q) {
            $q->whereIn('course_id', $this->assignedCourseIds())
                ->orWhereHas('course', fn (Builder $c) => $c->whereIn('id', $this->assignedCourseIds()));
        });
    }

    protected function enrolledLearnerIds(): Collection
    {
        return CourseEnrollment::query()
            ->whereIn('course_id', $this->assignedCourseIds())
            ->where('status', 'active')
            ->pluck('user_id')
            ->unique()
            ->values();
    }
}
