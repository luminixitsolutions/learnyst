<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Course;
use App\Models\CourseEnquiry;
use App\Models\CourseEnrollment;
use App\Models\CourseReview;
use App\Models\Lead;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicController extends Controller
{
    public function home()
    {
        $courses = Course::published()->with('category')->latest()->take(6)->get();
        $social = Setting::where('group', 'social')->pluck('value', 'key');

        return view('public.home', compact('courses', 'social'));
    }

    public function courses(Request $request)
    {
        $selectedCategory = $request->string('category')->toString();

        $categories = \App\Models\Category::query()
            ->where('is_active', true)
            ->with(['courses' => function ($query) {
                $query->published()
                    ->withCount(['sections', 'lessons'])
                    ->withAvg(['reviews as avg_rating' => fn ($q) => $q->where('status', 'approved')], 'rating')
                    ->withCount(['reviews as approved_reviews_count' => fn ($q) => $q->where('status', 'approved')])
                    ->latest();
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->filter(fn ($category) => $category->courses->isNotEmpty())
            ->values();

        if ($selectedCategory !== '') {
            $categories = $categories
                ->filter(fn ($category) => $category->slug === $selectedCategory)
                ->values();
        }

        $uncategorized = Course::published()
            ->whereNull('category_id')
            ->withCount(['sections', 'lessons'])
            ->withAvg(['reviews as avg_rating' => fn ($q) => $q->where('status', 'approved')], 'rating')
            ->withCount(['reviews as approved_reviews_count' => fn ($q) => $q->where('status', 'approved')])
            ->latest()
            ->get();

        $allCategories = \App\Models\Category::query()
            ->where('is_active', true)
            ->whereHas('courses', fn ($q) => $q->published())
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $totalCourses = Course::published()->count();

        return view('public.courses', compact(
            'categories',
            'uncategorized',
            'allCategories',
            'selectedCategory',
            'totalCourses'
        ));
    }

    public function courseShow(Course $course)
    {
        if ($course->status !== 'published') {
            abort(404);
        }

        $course->load([
            'sections.lessons',
            'category',
            'instructors',
            'faqs',
            'creator',
        ]);

        $reviews = $course->reviews()
            ->approved()
            ->latest()
            ->take(12)
            ->get();

        $avgRating = round((float) $course->reviews()->approved()->avg('rating'), 1);
        $reviewCount = $course->reviews()->approved()->count();

        $lessonCount = $course->sections->sum(fn ($section) => $section->lessons->count());
        $sectionCount = $course->sections->count();

        $institute = $course->created_by
            ? Company::query()->where('owner_user_id', $course->created_by)->first()
            : null;

        $relatedCourses = Course::published()
            ->with('category')
            ->where('id', '!=', $course->id)
            ->where(function ($query) use ($course) {
                if ($course->category_id) {
                    $query->where('category_id', $course->category_id);
                }
                if ($course->created_by) {
                    $query->orWhere('created_by', $course->created_by);
                }
            })
            ->latest()
            ->take(4)
            ->get();

        if ($relatedCourses->isEmpty()) {
            $relatedCourses = Course::published()
                ->with('category')
                ->where('id', '!=', $course->id)
                ->latest()
                ->take(4)
                ->get();
        }

        $isEnrolled = false;
        if (Auth::check() && Auth::user()->isLearner()) {
            $isEnrolled = CourseEnrollment::query()
                ->where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->where('status', 'active')
                ->exists();
        }

        return view('public.course-show', compact(
            'course',
            'reviews',
            'avgRating',
            'reviewCount',
            'lessonCount',
            'sectionCount',
            'institute',
            'relatedCourses',
            'isEnrolled'
        ));
    }

    public function storeCourseReview(Request $request, Course $course)
    {
        if ($course->status !== 'published') {
            abort(404);
        }

        $data = $request->validate([
            'reviewer_name' => ['required', 'string', 'max:120'],
            'reviewer_email' => ['nullable', 'email', 'max:255'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['required', 'string', 'max:2000'],
        ]);

        CourseReview::create([
            'course_id' => $course->id,
            'user_id' => Auth::id(),
            'reviewer_name' => $data['reviewer_name'],
            'reviewer_email' => $data['reviewer_email'] ?? Auth::user()?->email,
            'rating' => $data['rating'],
            'review' => $data['review'],
            'is_anonymous' => false,
            'status' => 'pending',
        ]);

        return redirect()
            ->to(route('public.course', $course).'#reviews')
            ->with('success', 'Thanks! Your review was submitted and will appear after approval.');
    }

    public function storeCourseEnquiry(Request $request, Course $course)
    {
        if ($course->status !== 'published') {
            abort(404);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'subject' => ['nullable', 'string', 'max:180'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        CourseEnquiry::create([
            'course_id' => $course->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'subject' => $data['subject'] ?? ('Enquiry about '.$course->title),
            'message' => $data['message'],
            'status' => 'new',
        ]);

        // Keep legacy lead capture in sync for marketing lists.
        Lead::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'course_id' => $course->id,
            'source' => 'course_enquiry',
            'status' => 'new',
            'notes' => $data['message'],
        ]);

        return redirect()
            ->to(route('public.course', $course).'#enquiry')
            ->with('success', 'Enquiry sent! The academy will contact you soon.');
    }

    public function captureLead(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string'],
            'course_id' => ['nullable', 'exists:courses,id'],
        ]);

        $validated['source'] = 'landing_page';
        Lead::create($validated);

        return back()->with('success', 'Thank you! We will contact you soon.');
    }
}
