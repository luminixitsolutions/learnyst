<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseSection;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $quizzes = CourseLesson::with('section.course')
            ->where('lesson_type', 'quiz')
            ->when($request->search, fn ($q) => $q->where('title', 'like', '%'.$request->search.'%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        return view('admin.quizzes.create', [
            'courses' => Course::with('sections')->orderBy('title')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'section_id' => ['required', 'exists:course_sections,id'],
            'title' => ['required', 'string', 'max:255'],
            'total_marks' => ['nullable', 'numeric', 'min:0'],
            'passing_marks' => ['nullable', 'numeric', 'min:0'],
            'time_limit' => ['nullable', 'integer', 'min:1'],
            'questions' => ['nullable', 'array'],
        ]);

        $lesson = CourseLesson::create([
            'course_section_id' => $validated['section_id'],
            'title' => $validated['title'],
            'lesson_type' => 'quiz',
            'quiz_data' => [
                'total_marks' => $validated['total_marks'] ?? null,
                'passing_marks' => $validated['passing_marks'] ?? null,
                'time_limit' => $validated['time_limit'] ?? null,
                'questions' => $validated['questions'] ?? [],
            ],
            'sort_order' => CourseSection::find($validated['section_id'])?->lessons()->max('sort_order') + 1,
        ]);

        ActivityLogger::log('quiz_created', "Quiz {$lesson->title} created", $lesson);

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz created.');
    }
}
