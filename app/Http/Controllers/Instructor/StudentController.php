<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Instructor\Concerns\ScopesToInstructor;
use App\Models\CourseEnrollment;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    use ScopesToInstructor;

    public function index(Request $request)
    {
        $courseIds = $this->assignedCourseIds();
        $learnerIds = CourseEnrollment::query()
            ->whereIn('course_id', $courseIds)
            ->when($request->filled('course_id'), fn ($q) => $q->where('course_id', (int) $request->course_id))
            ->pluck('user_id')
            ->unique();

        $students = User::query()
            ->whereIn('id', $learnerIds)
            ->withCount([
                'certificates as certificates_count',
            ])
            ->orderBy('name')
            ->paginate(30)
            ->withQueryString();

        $courses = $this->assignedCoursesQuery()->orderBy('title')->get(['id', 'title']);

        return view('instructor.students.index', compact('students', 'courses'));
    }

    public function show(User $user)
    {
        $courseIds = $this->assignedCourseIds();
        $enrollments = CourseEnrollment::with('course')
            ->where('user_id', $user->id)
            ->whereIn('course_id', $courseIds)
            ->get();

        abort_if($enrollments->isEmpty(), 403);

        $enrollments->load('course.sections.lessons');

        $progress = [];
        foreach ($enrollments as $enrollment) {
            $lessonIds = $enrollment->course?->sections->flatMap->lessons->pluck('id') ?? collect();
            $done = $lessonIds->isEmpty() ? 0 : LessonProgress::where('user_id', $user->id)
                ->whereIn('course_lesson_id', $lessonIds)
                ->where('is_completed', true)
                ->count();
            $total = $lessonIds->count();
            $progress[$enrollment->course_id] = [
                'done' => $done,
                'total' => $total,
                'pct' => $total > 0 ? round(($done / $total) * 100) : (float) ($enrollment->progress ?? 0),
            ];
        }

        return view('instructor.students.show', compact('user', 'enrollments', 'progress'));
    }
}
