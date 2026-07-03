<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Segment;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class SegmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Segment::withCount('users', 'courses');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $sort = $request->get('sort', 'latest');
        $query = match ($sort) {
            'title' => $query->orderBy('title'),
            'oldest' => $query->oldest(),
            default => $query->latest(),
        };

        $segments = $query->paginate(15)->withQueryString();

        return view('admin.segments.index', compact('segments', 'sort'));
    }

    public function create()
    {
        return view('admin.segments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:60'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        $segment = Segment::create($validated);

        ActivityLogger::log('segment_created', "Segment {$segment->title} created", $segment);

        return redirect()
            ->route('admin.segments.index')
            ->with('success', 'Segment created successfully.');
    }

    public function show(Segment $segment)
    {
        $segment->load(['users', 'courses']);
        $learners = User::whereHas('role', fn ($q) => $q->where('slug', 'learner'))->orderBy('name')->get();
        $courses = Course::orderBy('title')->get();

        return view('admin.segments.show', compact('segment', 'learners', 'courses'));
    }

    public function update(Request $request, Segment $segment)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:60'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        $segment->update($validated);

        return back()->with('success', 'Segment updated.');
    }

    public function destroy(Segment $segment)
    {
        $title = $segment->title;
        $segment->delete();

        ActivityLogger::log('segment_deleted', "Segment {$title} deleted");

        return redirect()->route('admin.segments.index')->with('success', 'Segment deleted.');
    }

    public function assignLearner(Request $request, Segment $segment)
    {
        $validated = $request->validate(['user_id' => ['required', 'exists:users,id']]);
        $segment->users()->syncWithoutDetaching([$validated['user_id']]);

        return back()->with('success', 'Learner assigned.');
    }

    public function assignCourse(Request $request, Segment $segment)
    {
        $validated = $request->validate(['course_id' => ['required', 'exists:courses,id']]);
        $segment->courses()->syncWithoutDetaching([$validated['course_id']]);

        return back()->with('success', 'Course assigned.');
    }
}
