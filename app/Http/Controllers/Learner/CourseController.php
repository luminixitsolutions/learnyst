<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\CommunityPost;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseLesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        $enrollments = CourseEnrollment::with('course.category')
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->paginate(12);

        return view('learner.courses.index', compact('enrollments'));
    }

    public function show(Course $course)
    {
        $enrollment = CourseEnrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->firstOrFail();

        $course->load(['sections.lessons']);

        return view('learner.courses.show', compact('course', 'enrollment'));
    }

    public function lesson(CourseLesson $lesson)
    {
        $lesson->load('section.course');
        $course = $lesson->section->course;

        CourseEnrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->firstOrFail();

        return view('learner.courses.lesson', compact('lesson', 'course'));
    }
}
