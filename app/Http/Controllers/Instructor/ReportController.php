<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Instructor\Concerns\ScopesToInstructor;
use App\Models\AssignmentSubmission;
use App\Models\CourseEnrollment;
use App\Models\Discussion;
use App\Models\LiveClassAttendance;
use App\Models\ScheduledEvent;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    use ScopesToInstructor;

    public function index()
    {
        $courseIds = $this->assignedCourseIds();

        $completion = CourseEnrollment::query()
            ->select('course_id', DB::raw('AVG(progress) as avg_progress'), DB::raw('COUNT(*) as learners'))
            ->whereIn('course_id', $courseIds)
            ->where('status', 'active')
            ->groupBy('course_id')
            ->with('course:id,title')
            ->get();

        $gradingBacklog = AssignmentSubmission::query()
            ->where('status', 'submitted')
            ->whereHas('lesson.section', fn ($q) => $q->whereIn('course_id', $courseIds))
            ->count();

        $attendanceCount = LiveClassAttendance::query()
            ->whereHas('event', function ($q) use ($courseIds) {
                $q->where('instructor_id', $this->instructorId())
                    ->orWhereIn('course_id', $courseIds);
            })
            ->count();

        $classesHeld = ScheduledEvent::query()
            ->where('type', 'class')
            ->where(function ($q) use ($courseIds) {
                $q->where('instructor_id', $this->instructorId())->orWhereIn('course_id', $courseIds);
            })
            ->whereIn('status', ['completed', 'live'])
            ->count();

        $openDoubts = Discussion::whereIn('course_id', $courseIds)->where('is_resolved', false)->count();

        $stats = [
            'learners' => CourseEnrollment::whereIn('course_id', $courseIds)->where('status', 'active')->distinct('user_id')->count('user_id'),
            'grading_backlog' => $gradingBacklog,
            'attendance' => $attendanceCount,
            'classes_held' => $classesHeld,
            'open_doubts' => $openDoubts,
        ];

        return view('instructor.reports.index', compact('completion', 'stats'));
    }
}
