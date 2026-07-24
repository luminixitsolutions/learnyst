<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseSection;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuizController extends Controller
{
    use ScopesToCurrentUser;

    public function index(Request $request)
    {
        $courseIds = $this->ownedCourseIds();

        $quizzes = CourseLesson::with('section.course')
            ->where('lesson_type', 'quiz')
            ->whereHas('section', fn ($q) => $q->whereIn('course_id', $courseIds))
            ->when($request->search, fn ($q) => $q->where('title', 'like', '%'.$request->search.'%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        return view('admin.quizzes.create', [
            'courses' => $this->owned(Course::query())->with('sections')->orderBy('title')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $ownedCourseIds = $this->ownedCourseIds();

        $validated = $request->validate([
            'course_id' => ['required', Rule::in($ownedCourseIds)],
            'section_id' => ['required', 'exists:course_sections,id'],
            'title' => ['required', 'string', 'max:255'],
            'total_marks' => ['nullable', 'numeric', 'min:0'],
            'passing_marks' => ['nullable', 'numeric', 'min:0'],
            'time_limit' => ['nullable', 'integer', 'min:1'],
            'questions' => ['nullable', 'array'],
        ]);

        $section = CourseSection::where('id', $validated['section_id'])
            ->whereIn('course_id', $ownedCourseIds)
            ->firstOrFail();

        $lesson = CourseLesson::create([
            'course_section_id' => $section->id,
            'title' => $validated['title'],
            'lesson_type' => 'quiz',
            'quiz_data' => [
                'total_marks' => $validated['total_marks'] ?? null,
                'passing_marks' => $validated['passing_marks'] ?? null,
                'time_limit' => $validated['time_limit'] ?? null,
                'questions' => $validated['questions'] ?? [],
            ],
            'sort_order' => $section->lessons()->max('sort_order') + 1,
        ]);

        ActivityLogger::log('quiz_created', "Quiz {$lesson->title} created", $lesson);

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz created.');
    }
}
