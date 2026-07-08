<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ExportsReportCsv;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseLesson;
use App\Models\CourseSection;
use App\Models\LiveClass;
use App\Models\Tag;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    use ExportsReportCsv;

    public function index(Request $request)
    {
        $query = Course::with(['category', 'creator'])->withCount('lessons')->latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('product_type')) {
            $query->where('product_type', $request->product_type);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('access_type')) {
            $query->where('access_type', $request->access_type);
        }

        if ($request->get('export') === '1') {
            $courses = $query->get();

            return $this->exportCsv('courses', [
                'Title', 'Type', 'Category', 'Status', 'Lessons', 'Enrollments', 'Price', 'Created',
            ], $courses->map(fn ($c) => [
                $c->title,
                $c->product_type,
                $c->category?->name ?? '—',
                $c->status,
                $c->lessons_count,
                $c->enrollment_count,
                $c->is_free ? 'Free' : $c->price,
                $c->created_at->format('Y-m-d'),
            ]));
        }

        $courses = $query->paginate(12)->withQueryString();
        $productTypes = ['course', 'ebook', 'podcast', 'webinar', 'custom', 'free_resource'];
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        $stats = [
            'total' => Course::count(),
            'active' => Course::where('status', 'published')->count(),
            'suspended' => Course::whereIn('status', ['unpublished', 'draft'])->count(),
            'enrolled_users' => CourseEnrollment::distinct('user_id')->count('user_id'),
        ];

        return view('admin.courses.index', compact('courses', 'productTypes', 'categories', 'stats'));
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
        $course->settings()->create([]);

        if ($request->filled('tags')) {
            $course->tags()->sync($request->tags);
        }

        if ($request->filled('instructor_ids')) {
            $sync = collect($request->instructor_ids)->mapWithKeys(fn ($id, $index) => [$id => ['is_primary' => $index === 0]])->all();
            $course->instructors()->sync($sync);
        }

        ActivityLogger::log('course_created', "Course {$course->title} created", $course);

        return redirect()->route('admin.courses.builder', $course)->with('success', 'Course created. Build your curriculum below.');
    }

    public function show(Course $course)
    {
        $course->load(['sections.lessons', 'category', 'instructors', 'enrollments.user']);

        return view('admin.courses.show', compact('course'));
    }

    public function builder(Course $course, Request $request)
    {
        $course->load(['sections.lessons', 'settings']);
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $instructors = User::whereHas('role', fn ($q) => $q->where('slug', 'instructor'))->orderBy('name')->get();
        $tab = $request->get('tab', 'curriculum');

        return view('admin.courses.builder', compact('course', 'categories', 'tags', 'instructors', 'tab'));
    }

    public function edit(Course $course)
    {
        return redirect()->route('admin.courses.builder', ['course' => $course, 'tab' => 'settings']);
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
        $course->load(['sections.lessons.liveClass', 'settings', 'tags']);

        $newCourse = $course->replicate();
        $newCourse->title = $course->title . ' (Copy)';
        $newCourse->slug = Str::slug($newCourse->title) . '-' . Str::random(5);
        $newCourse->status = 'draft';
        $newCourse->save();

        if ($course->settings) {
            $newSettings = $course->settings->replicate();
            $newSettings->course_id = $newCourse->id;
            $newSettings->save();
        } else {
            $newCourse->settings()->create([]);
        }

        foreach ($course->sections as $section) {
            $newSection = $section->replicate();
            $newSection->course_id = $newCourse->id;
            $newSection->save();

            foreach ($section->lessons as $lesson) {
                $newLesson = $lesson->replicate();
                $newLesson->course_section_id = $newSection->id;
                $newLesson->save();

                if ($lesson->liveClass) {
                    $newLive = $lesson->liveClass->replicate();
                    $newLive->course_lesson_id = $newLesson->id;
                    $newLive->save();
                }
            }
        }

        $newCourse->tags()->sync($course->tags->pluck('id'));

        ActivityLogger::log('course_duplicated', "Course duplicated from {$course->title}", $newCourse);

        return redirect()->route('admin.courses.builder', $newCourse)->with('success', 'Course duplicated successfully.');
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
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
            'drip_days' => ['nullable', 'integer', 'min:0'],
            'drip_date' => ['nullable', 'date'],
        ]);

        $validated['course_id'] = $course->id;
        $validated['sort_order'] = $validated['sort_order'] ?? ($course->sections()->max('sort_order') + 1);

        $section = CourseSection::create($validated);

        ActivityLogger::log('section_created', "Section {$section->title} added to {$course->title}", $section);

        return back()->with('success', 'Section added.');
    }

    public function updateSection(Request $request, CourseSection $section)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
            'drip_days' => ['nullable', 'integer', 'min:0'],
            'drip_date' => ['nullable', 'date'],
        ]);

        $section->update($validated);

        ActivityLogger::log('section_updated', "Section {$section->title} updated", $section);

        return back()->with('success', 'Section updated.');
    }

    public function storeLesson(Request $request, CourseSection $section)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:60'],
            'lesson_type' => ['required', 'in:video,audio,pdf,slides,text,quiz,assignment,live_class,external_link,code'],
        ]);

        $settings = null;
        if ($validated['lesson_type'] === 'slides') {
            $validated['lesson_type'] = 'pdf';
            $settings = ['sub_type' => 'slides'];
        }

        $validated['course_section_id'] = $section->id;
        $validated['sort_order'] = $section->lessons()->max('sort_order') + 1;
        $validated['status'] = 'draft';
        if ($settings) {
            $validated['settings'] = $settings;
        }

        $lesson = CourseLesson::create($validated);

        if ($lesson->lesson_type === 'live_class') {
            LiveClass::create(['course_lesson_id' => $lesson->id]);
        }

        ActivityLogger::log('lesson_created', "Lesson {$lesson->title} created", $lesson);

        return redirect()->route('admin.lessons.edit', $lesson);
    }

    public function reorderSections(Request $request, Course $course)
    {
        $request->validate(['order' => ['required', 'array'], 'order.*' => ['integer']]);

        foreach ($request->order as $index => $sectionId) {
            CourseSection::where('id', $sectionId)->where('course_id', $course->id)->update(['sort_order' => $index]);
        }

        ActivityLogger::log('sections_reordered', "Sections reordered for {$course->title}", $course);

        return response()->json(['success' => true]);
    }

    public function reorderLessons(Request $request, CourseSection $section)
    {
        $request->validate(['order' => ['required', 'array'], 'order.*' => ['integer']]);

        foreach ($request->order as $index => $lessonId) {
            CourseLesson::where('id', $lessonId)->where('course_section_id', $section->id)->update(['sort_order' => $index]);
        }

        ActivityLogger::log('lessons_reordered', "Lessons reordered in {$section->title}", $section);

        return response()->json(['success' => true]);
    }

    public function destroySection(CourseSection $section)
    {
        $title = $section->title;
        $course = $section->course;
        $section->delete();

        ActivityLogger::log('section_deleted', "Section {$title} deleted from {$course->title}", $course);

        return back()->with('success', 'Section deleted.');
    }

    public function destroyLesson(CourseLesson $lesson)
    {
        $title = $lesson->title;
        $course = $lesson->section->course;
        $lesson->delete();

        ActivityLogger::log('lesson_deleted', "Lesson {$title} deleted", $course);

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
