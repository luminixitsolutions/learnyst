<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lead;
use App\Models\Setting;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home()
    {
        $courses = Course::published()->with('category')->latest()->take(6)->get();
        $social = Setting::where('group', 'social')->pluck('value', 'key');

        return view('public.home', compact('courses', 'social'));
    }

    public function courses()
    {
        $courses = Course::published()->with('category')->paginate(12);

        return view('public.courses', compact('courses'));
    }

    public function courseShow(Course $course)
    {
        if ($course->status !== 'published') {
            abort(404);
        }

        $course->load(['sections.lessons', 'category', 'instructors']);

        return view('public.course-show', compact('course'));
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
