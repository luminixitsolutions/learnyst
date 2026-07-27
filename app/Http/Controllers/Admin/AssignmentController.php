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
            ->latest()
            ->get();

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

    public function edit(CourseLesson $lesson)
    {
        $lesson = $this->ownedAssignment($lesson);
        $lesson->load('section.course');

        return view('admin.assignments.edit', [
            'assignment' => $lesson,
            'courses' => $this->owned(Course::query())->with('sections')->orderBy('title')->get(),
        ]);
    }

    public function update(Request $request, CourseLesson $lesson)
    {
        $lesson = $this->ownedAssignment($lesson);
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

        $filePath = $lesson->file_path;
        if ($request->hasFile('file_path')) {
            $filePath = $request->file('file_path')->store('assignments', 'public');
        }

        $lesson->update([
            'course_section_id' => $section->id,
            'title' => $validated['title'],
            'content' => $validated['description'] ?? null,
            'file_path' => $filePath,
            'quiz_data' => [
                'due_date' => $validated['due_date'] ?? null,
                'marks' => $validated['marks'] ?? null,
                'status' => $validated['status'],
            ],
        ]);

        ActivityLogger::log('assignment_updated', "Assignment {$lesson->title} updated", $lesson);

        return redirect()->route('admin.assignments.index')->with('success', 'Assignment updated.');
    }

    public function destroy(CourseLesson $lesson)
    {
        $lesson = $this->ownedAssignment($lesson);
        $title = $lesson->title;
        $lesson->delete();

        ActivityLogger::log('assignment_deleted', "Assignment {$title} deleted", $lesson);

        return redirect()->route('admin.assignments.index')->with('success', 'Assignment deleted.');
    }

    protected function ownedAssignment(CourseLesson $lesson): CourseLesson
    {
        $lesson->loadMissing('section.course');
        abort_unless($lesson->lesson_type === 'assignment', 404);
        abort_unless($lesson->section?->course, 404);
        $this->authorizeOwner($lesson->section->course);

        return $lesson;
    }
}
