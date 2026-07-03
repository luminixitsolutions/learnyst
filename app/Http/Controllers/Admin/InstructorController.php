<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Course;
use App\Models\Role;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class InstructorController extends Controller
{
    public function index(Request $request)
    {
        $instructorRole = Role::where('slug', 'instructor')->first();

        $query = User::where('role_id', $instructorRole?->id)->withCount('courses')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $instructors = $query->paginate(15)->withQueryString();

        return view('admin.instructors.index', compact('instructors'));
    }

    public function create()
    {
        return view('admin.instructors.create');
    }

    public function store(Request $request)
    {
        $instructorRole = Role::where('slug', 'instructor')->firstOrFail();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string'],
            'bio' => ['nullable', 'string'],
            'password' => ['required', Password::defaults()],
        ]);

        $validated['role_id'] = $instructorRole->id;
        $validated['password'] = Hash::make($validated['password']);
        $validated['email_verified_at'] = now();
        $validated['is_active'] = $request->boolean('is_active', true);

        $instructor = User::create($validated);
        ActivityLogger::log('instructor_created', "Instructor {$instructor->name} created", $instructor);

        return redirect()->route('admin.instructors.index')->with('success', 'Instructor created.');
    }

    public function show(User $instructor)
    {
        $instructor->load(['courses']);
        $assignedBatches = Batch::where('instructor_id', $instructor->id)->with('course')->get();
        $availableCourses = Course::where('status', 'published')->whereNotIn('id', $instructor->courses->pluck('id'))->orderBy('title')->get();
        $availableBatches = Batch::whereNull('instructor_id')->orWhere('instructor_id', '!=', $instructor->id)->with('course')->orderBy('title')->get();

        return view('admin.instructors.show', compact('instructor', 'assignedBatches', 'availableCourses', 'availableBatches'));
    }

    public function edit(User $instructor)
    {
        return view('admin.instructors.edit', compact('instructor'));
    }

    public function update(Request $request, User $instructor)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $instructor->id],
            'phone' => ['nullable', 'string'],
            'bio' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $instructor->update($validated);

        return redirect()->route('admin.instructors.show', $instructor)->with('success', 'Instructor updated.');
    }

    public function destroy(User $instructor)
    {
        $instructor->delete();

        return redirect()->route('admin.instructors.index')->with('success', 'Instructor deleted.');
    }

    public function assignCourse(Request $request, User $instructor)
    {
        $validated = $request->validate(['course_id' => ['required', 'exists:courses,id']]);
        $instructor->courses()->syncWithoutDetaching([$validated['course_id'] => ['is_primary' => false]]);

        return back()->with('success', 'Course assigned to instructor.');
    }

    public function removeCourse(User $instructor, Course $course)
    {
        $instructor->courses()->detach($course->id);

        return back()->with('success', 'Course removed from instructor.');
    }

    public function assignBatch(Request $request, User $instructor)
    {
        $validated = $request->validate(['batch_id' => ['required', 'exists:batches,id']]);
        Batch::where('id', $validated['batch_id'])->update(['instructor_id' => $instructor->id]);

        return back()->with('success', 'Batch assigned to instructor.');
    }
}
