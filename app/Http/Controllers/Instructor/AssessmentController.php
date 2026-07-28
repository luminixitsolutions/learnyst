<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Instructor\Concerns\ScopesToInstructor;
use App\Models\AssignmentSubmission;
use App\Models\CourseLesson;
use App\Models\CourseSection;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AssessmentController extends Controller
{
    use ScopesToInstructor;

    public function index()
    {
        $assignments = $this->assignedLessonsQuery('assignment')
            ->with('section.course')
            ->withCount(['submissions as pending_count' => fn ($q) => $q->where('status', 'submitted')])
            ->latest()
            ->get();

        $quizzes = $this->assignedLessonsQuery('quiz')
            ->with('section.course')
            ->latest()
            ->get();

        return view('instructor.assessments.index', compact('assignments', 'quizzes'));
    }

    public function create(Request $request)
    {
        $type = $request->query('type', 'assignment');
        abort_unless(in_array($type, ['assignment', 'quiz'], true), 404);

        return view('instructor.assessments.form', [
            'type' => $type,
            'lesson' => new CourseLesson(['lesson_type' => $type]),
            'courses' => $this->assignedCoursesQuery()->with('sections')->orderBy('title')->get(),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $courseIds = $this->assignedCourseIds()->all();

        $validated = $request->validate([
            'lesson_type' => ['required', 'in:assignment,quiz'],
            'course_id' => ['required', Rule::in($courseIds)],
            'section_id' => ['required', 'exists:course_sections,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'marks' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:draft,published'],
            'file' => ['nullable', 'file', 'max:51200'],
        ]);

        $section = CourseSection::where('id', $validated['section_id'])
            ->whereIn('course_id', $courseIds)
            ->firstOrFail();

        $path = null;
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('assignments', 'public');
        }

        $lesson = CourseLesson::create([
            'course_section_id' => $section->id,
            'title' => $validated['title'],
            'lesson_type' => $validated['lesson_type'],
            'content' => $validated['description'] ?? null,
            'file_path' => $path,
            'quiz_data' => [
                'due_date' => $validated['due_date'] ?? null,
                'marks' => $validated['marks'] ?? null,
                'status' => $validated['status'],
            ],
            'sort_order' => ($section->lessons()->max('sort_order') ?? 0) + 1,
        ]);

        ActivityLogger::log('instructor_assessment_created', "{$validated['lesson_type']} created: {$lesson->title}", $lesson);

        return redirect()->route('instructor.assessments.index')->with('success', ucfirst($validated['lesson_type']).' created.');
    }

    public function submissions(CourseLesson $lesson)
    {
        abort_unless($lesson->lesson_type === 'assignment', 404);
        $courseId = $lesson->section?->course_id;
        abort_unless($courseId && $this->assignedCourseIds()->contains($courseId), 403);

        $submissions = AssignmentSubmission::with(['learner', 'grader'])
            ->where('course_lesson_id', $lesson->id)
            ->latest('submitted_at')
            ->paginate(30);

        return view('instructor.assessments.submissions', compact('lesson', 'submissions'));
    }

    public function grade(Request $request, AssignmentSubmission $submission)
    {
        $lesson = $submission->lesson;
        $courseId = $lesson?->section?->course_id;
        abort_unless($courseId && $this->assignedCourseIds()->contains($courseId), 403);

        $validated = $request->validate([
            'score' => ['required', 'numeric', 'min:0'],
            'feedback' => ['nullable', 'string', 'max:5000'],
            'allow_resubmit' => ['nullable', 'boolean'],
            'status' => ['required', 'in:graded,resubmit'],
        ]);

        $submission->update([
            'score' => $validated['score'],
            'feedback' => $validated['feedback'] ?? null,
            'allow_resubmit' => $request->boolean('allow_resubmit') || $validated['status'] === 'resubmit',
            'status' => $validated['status'],
            'graded_by' => Auth::id(),
            'graded_at' => now(),
        ]);

        ActivityLogger::log('instructor_submission_graded', "Graded submission #{$submission->id}", $submission);

        return back()->with('success', 'Submission graded.');
    }
}
