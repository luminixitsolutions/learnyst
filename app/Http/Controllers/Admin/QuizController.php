<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseSection;
use App\Services\ActivityLogger;
use App\Services\AiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class QuizController extends Controller
{
    use ScopesToCurrentUser;

    public function __construct(protected AiService $ai) {}

    public function index(Request $request)
    {
        $courseIds = $this->ownedCourseIds();

        $quizzes = CourseLesson::with('section.course')
            ->where('lesson_type', 'quiz')
            ->whereHas('section', fn ($q) => $q->whereIn('course_id', $courseIds))
            ->latest()
            ->get();

        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        return view('admin.quizzes.create', [
            'courses' => $this->owned(Course::query())->with('sections')->orderBy('title')->get(),
        ]);
    }

    public function aiAnalyze(Request $request)
    {
        $ownedCourseIds = $this->ownedCourseIds();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'brief' => ['nullable', 'string', 'max:2000'],
            'course_id' => ['nullable', Rule::in($ownedCourseIds)],
            'question_count' => ['nullable', 'integer', 'min:3', 'max:15'],
        ]);

        $courseTitle = null;
        if (! empty($validated['course_id'])) {
            $courseTitle = $this->owned(Course::query())->whereKey($validated['course_id'])->value('title');
        }

        try {
            $details = $this->ai->generateQuizDetails(
                Auth::user(),
                $validated['title'],
                $validated['brief'] ?? null,
                $courseTitle,
                (int) ($validated['question_count'] ?? 5)
            );
        } catch (ValidationException $e) {
            return response()->json([
                'ok' => false,
                'message' => $e->errors()['ai'][0] ?? 'AI request failed.',
                'errors' => $e->errors(),
            ], 422);
        }

        \App\Models\AiGeneration::create([
            'created_by' => Auth::id(),
            'user_id' => Auth::id(),
            'feature' => 'quiz_details',
            'title' => $validated['title'],
            'prompt' => 'Title: '.$validated['title']
                .(! empty($courseTitle) ? "\nCourse: ".$courseTitle : '')
                .(! empty($validated['brief']) ? "\nBrief: ".$validated['brief'] : ''),
            'output' => json_encode($details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            'status' => 'draft',
            'meta' => ['source' => 'quiz_create_form'],
        ]);

        ActivityLogger::log('ai_quiz_details', 'AI filled quiz details for: '.$validated['title']);

        return response()->json([
            'ok' => true,
            'message' => 'Quiz details generated. Review questions and save when ready.',
            'data' => $details,
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
            'questions.*.question' => ['nullable', 'string', 'max:2000'],
            'questions.*.options' => ['nullable', 'array'],
            'questions.*.options.*' => ['nullable', 'string', 'max:500'],
            'questions.*.correct' => ['nullable', 'integer', 'min:0', 'max:3'],
            'questions.*.marks' => ['nullable', 'numeric', 'min:0'],
        ]);

        $section = CourseSection::where('id', $validated['section_id'])
            ->whereIn('course_id', $ownedCourseIds)
            ->firstOrFail();

        $questions = $this->normalizeQuestions($validated['questions'] ?? []);

        $lesson = CourseLesson::create([
            'course_section_id' => $section->id,
            'title' => $validated['title'],
            'lesson_type' => 'quiz',
            'quiz_data' => [
                'total_marks' => $validated['total_marks'] ?? null,
                'passing_marks' => $validated['passing_marks'] ?? null,
                'time_limit' => $validated['time_limit'] ?? null,
                'questions' => $questions,
            ],
            'sort_order' => $section->lessons()->max('sort_order') + 1,
        ]);

        ActivityLogger::log('quiz_created', "Quiz {$lesson->title} created", $lesson);

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz created.');
    }

    public function edit(CourseLesson $lesson)
    {
        $lesson = $this->ownedQuiz($lesson);
        $lesson->load('section.course');

        return view('admin.quizzes.edit', [
            'quiz' => $lesson,
            'courses' => $this->owned(Course::query())->with('sections')->orderBy('title')->get(),
        ]);
    }

    public function update(Request $request, CourseLesson $lesson)
    {
        $lesson = $this->ownedQuiz($lesson);
        $ownedCourseIds = $this->ownedCourseIds();

        $validated = $request->validate([
            'course_id' => ['required', Rule::in($ownedCourseIds)],
            'section_id' => ['required', 'exists:course_sections,id'],
            'title' => ['required', 'string', 'max:255'],
            'total_marks' => ['nullable', 'numeric', 'min:0'],
            'passing_marks' => ['nullable', 'numeric', 'min:0'],
            'time_limit' => ['nullable', 'integer', 'min:1'],
            'questions' => ['nullable', 'array'],
            'questions.*.question' => ['nullable', 'string', 'max:2000'],
            'questions.*.options' => ['nullable', 'array'],
            'questions.*.options.*' => ['nullable', 'string', 'max:500'],
            'questions.*.correct' => ['nullable', 'integer', 'min:0', 'max:3'],
            'questions.*.marks' => ['nullable', 'numeric', 'min:0'],
        ]);

        $section = CourseSection::where('id', $validated['section_id'])
            ->whereIn('course_id', $ownedCourseIds)
            ->firstOrFail();

        $questions = array_key_exists('questions', $validated)
            ? $this->normalizeQuestions($validated['questions'] ?? [])
            : ($lesson->quiz_data['questions'] ?? []);

        $lesson->update([
            'course_section_id' => $section->id,
            'title' => $validated['title'],
            'quiz_data' => [
                'total_marks' => $validated['total_marks'] ?? null,
                'passing_marks' => $validated['passing_marks'] ?? null,
                'time_limit' => $validated['time_limit'] ?? null,
                'questions' => $questions,
            ],
        ]);

        ActivityLogger::log('quiz_updated', "Quiz {$lesson->title} updated", $lesson);

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz updated.');
    }

    public function destroy(CourseLesson $lesson)
    {
        $lesson = $this->ownedQuiz($lesson);
        $title = $lesson->title;
        $lesson->delete();

        ActivityLogger::log('quiz_deleted', "Quiz {$title} deleted", $lesson);

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz deleted.');
    }

    /**
     * @param  array<int, mixed>  $questions
     * @return array<int, array{question: string, options: array<int, string>, correct: int, marks: float}>
     */
    protected function normalizeQuestions(array $questions): array
    {
        $normalized = [];

        foreach ($questions as $item) {
            if (is_string($item)) {
                $trimmed = trim($item);
                if ($trimmed !== '') {
                    $normalized[] = [
                        'question' => $trimmed,
                        'options' => ['Option A', 'Option B', 'Option C', 'Option D'],
                        'correct' => 0,
                        'marks' => 1,
                    ];
                }
                continue;
            }

            if (! is_array($item)) {
                continue;
            }

            $text = trim((string) ($item['question'] ?? $item['text'] ?? ''));
            if ($text === '') {
                continue;
            }

            $options = array_values(array_map(
                fn ($o) => trim((string) $o),
                array_slice((array) ($item['options'] ?? []), 0, 4)
            ));
            while (count($options) < 4) {
                $options[] = 'Option '.chr(65 + count($options));
            }

            $correct = isset($item['correct']) ? (int) $item['correct'] : 0;
            if ($correct < 0 || $correct > 3) {
                $correct = 0;
            }

            $normalized[] = [
                'question' => $text,
                'options' => $options,
                'correct' => $correct,
                'marks' => isset($item['marks']) ? (float) $item['marks'] : 1,
            ];
        }

        return $normalized;
    }

    protected function ownedQuiz(CourseLesson $lesson): CourseLesson
    {
        $lesson->loadMissing('section.course');
        abort_unless($lesson->lesson_type === 'quiz', 404);
        abort_unless($lesson->section?->course, 404);
        $this->authorizeOwner($lesson->section->course);

        return $lesson;
    }
}
