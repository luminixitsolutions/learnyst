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

class AssignmentController extends Controller
{
    use ScopesToCurrentUser;

    public function index(Request $request)
    {
        $courseIds = $this->ownedCourseIds();

        $assignments = CourseLesson::with('section.course')
            ->where('lesson_type', 'assignment')
            ->whereHas('section', fn ($q) => $q->whereIn('course_id', $courseIds))
            ->when($request->search, fn ($q) => $q->where('title', 'like', '%'.$request->search.'%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.assignments.index', compact('assignments'));
    }

    public function create()
    {
        return view('admin.assignments.create', [
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
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'marks' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:draft,published'],
            'file_path' => ['nullable', 'file', 'max:51200'],
        ]);

        $section = CourseSection::where('id', $validated['section_id'])
            ->whereIn('course_id', $ownedCourseIds)
            ->firstOrFail();

        if ($request->hasFile('file_path')) {
            $validated['file_path'] = $request->file('file_path')->store('assignments', 'public');
        }

        $lesson = CourseLesson::create([
            'course_section_id' => $section->id,
            'title' => $validated['title'],
            'lesson_type' => 'assignment',
            'content' => $validated['description'] ?? null,
            'file_path' => $validated['file_path'] ?? null,
            'quiz_data' => [
                'due_date' => $validated['due_date'] ?? null,
                'marks' => $validated['marks'] ?? null,
                'status' => $validated['status'],
            ],
            'sort_order' => $section->lessons()->max('sort_order') + 1,
        ]);

        ActivityLogger::log('assignment_created', "Assignment {$lesson->title} created", $lesson);

        return redirect()->route('admin.assignments.index')->with('success', 'Assignment created.');
    }
}
