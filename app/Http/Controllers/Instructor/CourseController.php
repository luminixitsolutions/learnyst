<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Instructor\Concerns\ScopesToInstructor;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseSection;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    use ScopesToInstructor;

    public function index()
    {
        $courses = $this->assignedCoursesQuery()
            ->withCount(['enrollments', 'sections'])
            ->orderBy('title')
            ->paginate(20);

        return view('instructor.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('instructor.courses.form', [
            'course' => new Course(['status' => 'draft', 'is_free' => true]),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published,unpublished'],
            'is_free' => ['nullable', 'boolean'],
            'price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $course = Course::create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'description' => $validated['description'] ?? null,
            'slug' => Str::slug($validated['title']).'-'.Str::random(4),
            'status' => $validated['status'],
            'is_free' => $request->boolean('is_free', true),
            'price' => $validated['price'] ?? 0,
            'created_by' => $this->instituteOwnerId(),
        ]);

        $course->instructors()->attach(Auth::id(), ['is_primary' => true]);

        ActivityLogger::log('instructor_course_created', "Instructor created course: {$course->title}", $course);

        return redirect()->route('instructor.courses.show', $course)->with('success', 'Course created and assigned to you.');
    }

    public function show(Course $course)
    {
        $this->assertAssignedCourse($course);
        $course->load(['sections.lessons', 'instructors']);
        $course->loadCount('enrollments');

        return view('instructor.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $this->assertAssignedCourse($course);

        return view('instructor.courses.form', [
            'course' => $course,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, Course $course)
    {
        $this->assertAssignedCourse($course);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published,unpublished'],
            'is_free' => ['nullable', 'boolean'],
            'price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $course->update([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'is_free' => $request->boolean('is_free'),
            'price' => $validated['price'] ?? $course->price,
        ]);

        ActivityLogger::log('instructor_course_updated', "Instructor updated course: {$course->title}", $course);

        return redirect()->route('instructor.courses.show', $course)->with('success', 'Course updated.');
    }

    public function storeSection(Request $request, Course $course)
    {
        $this->assertAssignedCourse($course);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $section = CourseSection::create([
            'course_id' => $course->id,
            'title' => $validated['title'],
            'sort_order' => ($course->sections()->max('sort_order') ?? 0) + 1,
        ]);

        ActivityLogger::log('instructor_section_created', "Section added: {$section->title}", $course);

        return back()->with('success', 'Section added.');
    }

    public function storeLesson(Request $request, Course $course, CourseSection $section)
    {
        $this->assertAssignedCourse($course);
        abort_unless((int) $section->course_id === (int) $course->id, 404);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'lesson_type' => ['required', 'in:video,text,pdf,quiz,assignment,live_class'],
            'content' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:102400'],
        ]);

        $path = null;
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('lessons', 'public');
        }

        CourseLesson::create([
            'course_section_id' => $section->id,
            'title' => $validated['title'],
            'lesson_type' => $validated['lesson_type'],
            'content' => $validated['content'] ?? null,
            'file_path' => $path,
            'video_url' => null,
            'sort_order' => ($section->lessons()->max('sort_order') ?? 0) + 1,
        ]);

        ActivityLogger::log('instructor_lesson_created', "Lesson added: {$validated['title']}", $course);

        return back()->with('success', 'Lesson added.');
    }

    public function updateLesson(Request $request, Course $course, CourseLesson $lesson)
    {
        $this->assertAssignedCourse($course);
        abort_unless((int) $lesson->section?->course_id === (int) $course->id, 404);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'file' => ['nullable', 'file', 'max:102400'],
        ]);

        if ($request->hasFile('file')) {
            $validated['file_path'] = $request->file('file')->store('lessons', 'public');
        }

        $lesson->update(collect($validated)->except('file')->all());

        return back()->with('success', 'Lesson updated.');
    }
}
