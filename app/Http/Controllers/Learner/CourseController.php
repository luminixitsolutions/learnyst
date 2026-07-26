<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseLesson;
use App\Models\LessonProgress;
use App\Services\ActivityLogger;
use App\Services\CertificateDesignService;
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
        $enrollment = $this->activeEnrollment($course->id);

        if (! $enrollment) {
            return redirect()
                ->route('public.course', $course)
                ->with('error', $course->requiresPayment()
                    ? 'Please complete payment to access this course.'
                    : 'Please enroll to access this course.');
        }

        $course->load(['sections.lessons']);

        $completedLessonIds = LessonProgress::where('user_id', Auth::id())
            ->where('is_completed', true)
            ->whereIn('course_lesson_id', $course->lessons()->pluck('course_lessons.id'))
            ->pluck('course_lesson_id')
            ->all();

        $certificate = Certificate::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->first();

        return view('learner.courses.show', compact('course', 'enrollment', 'completedLessonIds', 'certificate'));
    }

    public function issueCertificate(Course $course)
    {
        $enrollment = $this->activeEnrollment($course->id);

        if (! $enrollment) {
            return redirect()
                ->route('public.course', $course)
                ->with('error', 'Please enroll to access this course.');
        }

        if (! $this->hasCompletedAllLessons($course, Auth::id())) {
            return back()->with('error', 'Complete all lessons before issuing your certificate.');
        }

        $certificate = Certificate::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'course_id' => $course->id,
            ],
            [
                'certificate_template_id' => app(CertificateDesignService::class)->forCourse($course)->id,
                'issued_at' => now(),
            ]
        );

        if (! $certificate->issued_at) {
            $certificate->update(['issued_at' => now()]);
        }

        if ($enrollment->progress < 100) {
            $this->recalculateEnrollmentProgress($enrollment, $course);
        }

        ActivityLogger::log('certificate_issued', "Certificate {$certificate->certificate_number} issued", $certificate);

        return redirect()
            ->route('learner.certificates.download', $certificate)
            ->with('success', 'Certificate issued successfully.');
    }

    public function downloadCertificate(Certificate $certificate)
    {
        if ((int) $certificate->user_id !== (int) Auth::id()) {
            abort(403);
        }

        $certificate->load(['user', 'course.settings', 'template']);

        $design = app(CertificateDesignService::class);
        $template = $certificate->course
            ? $design->forCourse($certificate->course)
            : $certificate->template;

        $layout = $template ? $design->layoutFrom($template) : $design->defaultLayout();
        $html = ($template && filled($template->html_content))
            ? $template->html_content
            : $design->compileHtml($layout);
        $replacements = $design->replacements($certificate);

        return view('learner.certificates.download', compact('certificate', 'layout', 'html', 'replacements'));
    }

    public function lesson(CourseLesson $lesson)
    {
        $lesson->load('section.course');
        $course = $lesson->section->course;

        $enrollment = $this->activeEnrollment($course->id);

        if (! $enrollment) {
            return redirect()
                ->route('public.course', $course)
                ->with('error', 'Please enroll to access this lesson.');
        }

        $progress = LessonProgress::where('user_id', Auth::id())
            ->where('course_lesson_id', $lesson->id)
            ->first();

        $isCompleted = (bool) ($progress?->is_completed);

        return view('learner.courses.lesson', compact('lesson', 'course', 'enrollment', 'isCompleted'));
    }

    public function complete(CourseLesson $lesson)
    {
        $lesson->load('section.course');
        $course = $lesson->section->course;
        $enrollment = $this->activeEnrollment($course->id);

        if (! $enrollment) {
            return redirect()
                ->route('public.course', $course)
                ->with('error', 'Please enroll to access this lesson.');
        }

        LessonProgress::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'course_lesson_id' => $lesson->id,
            ],
            [
                'is_completed' => true,
                'completed_at' => now(),
            ]
        );

        $this->recalculateEnrollmentProgress($enrollment, $course);

        ActivityLogger::log('lesson_completed', "Completed lesson {$lesson->title}", $lesson);

        return back()->with('success', 'Lesson marked as complete.');
    }

    public function incomplete(CourseLesson $lesson)
    {
        $lesson->load('section.course');
        $course = $lesson->section->course;
        $enrollment = $this->activeEnrollment($course->id);

        if (! $enrollment) {
            return redirect()
                ->route('public.course', $course)
                ->with('error', 'Please enroll to access this lesson.');
        }

        LessonProgress::where('user_id', Auth::id())
            ->where('course_lesson_id', $lesson->id)
            ->update([
                'is_completed' => false,
                'completed_at' => null,
            ]);

        $this->recalculateEnrollmentProgress($enrollment, $course);

        return back()->with('success', 'Lesson marked as incomplete.');
    }

    protected function activeEnrollment(int $courseId): ?CourseEnrollment
    {
        return CourseEnrollment::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->where('status', 'active')
            ->first();
    }

    protected function hasCompletedAllLessons(Course $course, int $userId): bool
    {
        $totalLessons = $course->lessons()->count();

        if ($totalLessons === 0) {
            return false;
        }

        $completedCount = LessonProgress::where('user_id', $userId)
            ->where('is_completed', true)
            ->whereIn('course_lesson_id', $course->lessons()->pluck('course_lessons.id'))
            ->count();

        return $completedCount >= $totalLessons;
    }

    protected function recalculateEnrollmentProgress(CourseEnrollment $enrollment, Course $course): void
    {
        $totalLessons = $course->lessons()->count();
        if ($totalLessons === 0) {
            $enrollment->update(['progress' => 0, 'completed_at' => null]);

            return;
        }

        $completedCount = LessonProgress::where('user_id', $enrollment->user_id)
            ->where('is_completed', true)
            ->whereIn('course_lesson_id', $course->lessons()->pluck('course_lessons.id'))
            ->count();

        $progress = round(($completedCount / $totalLessons) * 100, 2);

        $enrollment->update([
            'progress' => $progress,
            'completed_at' => $progress >= 100 ? ($enrollment->completed_at ?? now()) : null,
        ]);
    }
}
