<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseSection;
use App\Models\Tag;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with(['category', 'creator'])->latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('product_type')) {
            $query->where('product_type', $request->product_type);
        }

        $courses = $query->paginate(15)->withQueryString();
        $productTypes = ['course', 'ebook', 'podcast', 'webinar', 'custom', 'free_resource'];

        return view('admin.courses.index', compact('courses', 'productTypes'));
    }

    public function create(Request $request)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $instructors = User::whereHas('role', fn ($q) => $q->where('slug', 'instructor'))->orderBy('name')->get();
        $productType = $request->get('type', 'course');

        return view('admin.courses.create', compact('categories', 'tags', 'instructors', 'productType'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateCourse($request);

        $validated['is_free'] = $request->boolean('is_free');
        $validated['drip_enabled'] = $request->boolean('drip_enabled');

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses', 'public');
        }

        $validated['created_by'] = Auth::id();
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);

        $course = Course::create($validated);

        if ($request->filled('tags')) {
            $course->tags()->sync($request->tags);
        }

        if ($request->filled('instructor_ids')) {
            $sync = collect($request->instructor_ids)->mapWithKeys(fn ($id, $index) => [$id => ['is_primary' => $index === 0]])->all();
            $course->instructors()->sync($sync);
        }

        ActivityLogger::log('course_created', "Course {$course->title} created", $course);

        return redirect()->route('admin.courses.edit', $course)->with('success', 'Course created. Add curriculum below.');
    }

    public function show(Course $course)
    {
        $course->load(['sections.lessons', 'category', 'instructors', 'enrollments.user']);

        return view('admin.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $course->load('sections.lessons');
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $instructors = User::whereHas('role', fn ($q) => $q->where('slug', 'instructor'))->orderBy('name')->get();

        return view('admin.courses.edit', compact('course', 'categories', 'tags', 'instructors'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $this->validateCourse($request, $course->id);

        $validated['is_free'] = $request->boolean('is_free');
        $validated['drip_enabled'] = $request->boolean('drip_enabled');

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses', 'public');
        }

        $course->update($validated);
        $course->tags()->sync($request->tags ?? []);
        $course->instructors()->sync(
            collect($request->instructor_ids ?? [])->mapWithKeys(fn ($id, $index) => [$id => ['is_primary' => $index === 0]])->all()
        );

        ActivityLogger::log('course_updated', "Course {$course->title} updated", $course);

        return back()->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        ActivityLogger::log('course_deleted', "Course {$course->title} deleted", $course);
        $course->delete();

        return redirect()->route('admin.courses.index')->with('success', 'Course deleted.');
    }

    public function duplicate(Course $course)
    {
        $newCourse = $course->replicate();
        $newCourse->title = $course->title . ' (Copy)';
        $newCourse->slug = Str::slug($newCourse->title) . '-' . Str::random(5);
        $newCourse->status = 'draft';
        $newCourse->save();

        foreach ($course->sections as $section) {
            $newSection = $section->replicate();
            $newSection->course_id = $newCourse->id;
            $newSection->save();

            foreach ($section->lessons as $lesson) {
                $newLesson = $lesson->replicate();
                $newLesson->course_section_id = $newSection->id;
                $newLesson->save();
            }
        }

        $newCourse->tags()->sync($course->tags->pluck('id'));

        ActivityLogger::log('course_duplicated', "Course duplicated from {$course->title}", $newCourse);

        return redirect()->route('admin.courses.edit', $newCourse)->with('success', 'Course duplicated successfully.');
    }

    public function publish(Course $course)
    {
        $course->update(['status' => 'published']);
        ActivityLogger::log('course_published', "Course {$course->title} published", $course);

        return back()->with('success', 'Course published.');
    }

    public function unpublish(Course $course)
    {
        $course->update(['status' => 'unpublished']);

        return back()->with('success', 'Course unpublished.');
    }

    public function storeSection(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'drip_days' => ['nullable', 'integer', 'min:0'],
            'drip_date' => ['nullable', 'date'],
        ]);

        $validated['course_id'] = $course->id;
        $validated['sort_order'] = $course->sections()->max('sort_order') + 1;

        CourseSection::create($validated);

        return back()->with('success', 'Section added.');
    }

    public function storeLesson(Request $request, CourseSection $section)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'lesson_type' => ['required', 'in:video,pdf,text,quiz,assignment,live_class'],
            'content' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url'],
            'file_path' => ['nullable', 'file', 'max:51200'],
            'duration_minutes' => ['nullable', 'integer'],
            'is_preview' => ['boolean'],
            'is_locked' => ['boolean'],
            'drip_date' => ['nullable', 'date'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($request->hasFile('file_path')) {
            $validated['file_path'] = $request->file('file_path')->store('lessons', 'public');
        }

        $validated['course_section_id'] = $section->id;
        $validated['sort_order'] = $request->input('sort_order') ?? ($section->lessons()->max('sort_order') + 1);
        $validated['is_preview'] = $request->boolean('is_preview');
        $validated['is_locked'] = $request->boolean('is_locked');

        CourseLesson::create($validated);

        return back()->with('success', 'Lesson added.');
    }

    public function destroySection(CourseSection $section)
    {
        $section->delete();

        return back()->with('success', 'Section deleted.');
    }

    public function destroyLesson(CourseLesson $lesson)
    {
        $lesson->delete();

        return back()->with('success', 'Lesson deleted.');
    }

    protected function validateCourse(Request $request, ?int $courseId = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'product_type' => ['required', 'in:course,ebook,podcast,webinar,custom,free_resource'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'is_free' => ['boolean'],
            'access_type' => ['required', 'in:free,trial,paid'],
            'start_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'validity_days' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', 'in:draft,published,unpublished'],
            'drip_enabled' => ['boolean'],
            'intro_video_url' => ['nullable', 'url'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'instructor_ids' => ['nullable', 'array'],
            'instructor_ids.*' => ['exists:users,id'],
        ]);
    }
}
