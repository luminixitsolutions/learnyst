<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Instructor\Concerns\ScopesToInstructor;
use App\Models\AssignmentSubmission;
use App\Models\CourseEnrollment;
use App\Models\Discussion;
use App\Models\InstructorTask;
use App\Models\ScheduledEvent;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    use ScopesToInstructor;

    public function index()
    {
        $user = Auth::user();
        $courseIds = $this->assignedCourseIds();

        $courses = $user->courses()->withCount('enrollments')->orderBy('title')->get();
        $upcomingClasses = $this->assignedEventsQuery()
            ->where('starts_at', '>=', now())
            ->whereIn('status', ['scheduled', 'live'])
            ->orderBy('starts_at')
            ->take(6)
            ->get();

        $pendingSubmissions = AssignmentSubmission::query()
            ->with(['learner', 'lesson.section.course'])
            ->where('status', 'submitted')
            ->whereHas('lesson.section', fn ($q) => $q->whereIn('course_id', $courseIds))
            ->latest('submitted_at')
            ->take(8)
            ->get();

        $openDiscussions = $this->assignedDiscussionsQuery()
            ->with(['user', 'course'])
            ->where('is_resolved', false)
            ->latest()
            ->take(6)
            ->get();

        $tasks = InstructorTask::where('user_id', $user->id)
            ->where('status', '!=', 'completed')
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'courses' => $courses->count(),
            'learners' => CourseEnrollment::whereIn('course_id', $courseIds)->where('status', 'active')->distinct('user_id')->count('user_id'),
            'upcoming' => ScheduledEvent::query()->where('type', 'class')->where('instructor_id', $user->id)->where('starts_at', '>=', now())->count(),
            'grading' => AssignmentSubmission::query()
                ->where('status', 'submitted')
                ->whereHas('lesson.section', fn ($q) => $q->whereIn('course_id', $courseIds))
                ->count(),
            'doubts' => Discussion::query()->whereIn('course_id', $courseIds)->where('is_resolved', false)->count(),
        ];

        return view('instructor.dashboard', compact(
            'courses', 'upcomingClasses', 'pendingSubmissions', 'openDiscussions', 'tasks', 'stats'
        ));
    }
}
