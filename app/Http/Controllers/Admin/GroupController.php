<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Group;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $query = Group::withCount('learners', 'courses')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $groups = $query->paginate(15)->withQueryString();

        return view('admin.groups.index', compact('groups'));
    }

    public function create()
    {
        return view('admin.groups.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_active'] = $request->boolean('is_active', true);

        $group = Group::create($validated);
        ActivityLogger::log('group_created', "Group {$group->name} created", $group);

        return redirect()->route('admin.groups.show', $group)->with('success', 'Group created.');
    }

    public function show(Group $group)
    {
        $group->load(['learners', 'courses']);
        $availableLearners = User::whereHas('role', fn ($q) => $q->where('slug', 'learner'))
            ->whereNotIn('id', $group->learners->pluck('id'))
            ->orderBy('name')->get();
        $courses = Course::where('status', 'published')->orderBy('title')->get();

        return view('admin.groups.show', compact('group', 'availableLearners', 'courses'));
    }

    public function edit(Group $group)
    {
        return view('admin.groups.edit', compact('group'));
    }

    public function update(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $group->update($validated);

        return redirect()->route('admin.groups.show', $group)->with('success', 'Group updated.');
    }

    public function destroy(Group $group)
    {
        $group->delete();

        return redirect()->route('admin.groups.index')->with('success', 'Group deleted.');
    }

    public function addLearner(Request $request, Group $group)
    {
        $validated = $request->validate(['user_id' => ['required', 'exists:users,id']]);
        $group->learners()->syncWithoutDetaching([$validated['user_id']]);

        return back()->with('success', 'Learner added to group.');
    }

    public function removeLearner(Group $group, User $user)
    {
        $group->learners()->detach($user->id);

        return back()->with('success', 'Learner removed from group.');
    }

    public function assignCourse(Request $request, Group $group)
    {
        $validated = $request->validate(['course_id' => ['required', 'exists:courses,id']]);
        $group->courses()->syncWithoutDetaching([$validated['course_id']]);

        return back()->with('success', 'Course assigned to group.');
    }

    public function removeCourse(Group $group, Course $course)
    {
        $group->courses()->detach($course->id);

        return back()->with('success', 'Course removed from group.');
    }
}
