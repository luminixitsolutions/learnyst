<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Course;
use App\Models\Role;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\AiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class InstructorController extends Controller
{
    use ScopesToCurrentUser;

    public function __construct(protected AiService $ai) {}

    public function index(Request $request)
    {
        $query = $this->ownedUsersQuery('instructor')->withCount('courses')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $instructors = $query->get();

        return view('admin.instructors.index', compact('instructors'));
    }

    public function create()
    {
        return view('admin.instructors.create');
    }

    public function aiAnalyze(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'profession' => ['required', 'string', 'max:255'],
            'brief' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $details = $this->ai->generateInstructorBio(
                Auth::user(),
                $validated['name'],
                $validated['profession'],
                $validated['brief'] ?? null
            );
        } catch (ValidationException $e) {
            return response()->json([
                'ok' => false,
                'message' => $e->errors()['ai'][0] ?? 'AI request failed.',
                'errors' => $e->errors(),
            ], 422);
        }

        \App\Models\AiGeneration::create([
            'created_by' => Auth::id(),
            'user_id' => Auth::id(),
            'feature' => 'instructor_bio',
            'title' => $validated['name'].' — '.$validated['profession'],
            'prompt' => 'Name: '.$validated['name']
                ."\nProfession: ".$validated['profession']
                .(! empty($validated['brief']) ? "\nBrief: ".$validated['brief'] : ''),
            'output' => json_encode($details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            'status' => 'draft',
            'meta' => ['source' => 'instructor_create_form'],
        ]);

        ActivityLogger::log('ai_instructor_bio', 'AI filled instructor bio for: '.$validated['name']);

        return response()->json([
            'ok' => true,
            'message' => 'Bio generated from profession. Review and save when ready.',
            'data' => $details,
        ]);
    }

    public function store(Request $request)
    {
        $instructorRole = Role::where('slug', 'instructor')->firstOrFail();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string'],
            'expertise' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'password' => ['required', Password::defaults()],
        ]);

        $validated['role_id'] = $instructorRole->id;
        $validated['password'] = Hash::make($validated['password']);
        $validated['email_verified_at'] = now();
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['created_by'] = Auth::id();

        $instructor = User::create($validated);
        ActivityLogger::log('instructor_created', "Instructor {$instructor->name} created", $instructor);

        return redirect()->route('admin.instructors.index')->with('success', 'Instructor created.');
    }

    public function show(User $instructor)
    {
        $this->authorizeOwner($instructor);
        $instructor->load(['courses']);

        $ownedCourseIds = $this->ownedCourseIds();
        $assignedBatches = Batch::where('instructor_id', $instructor->id)
            ->whereIn('course_id', $ownedCourseIds)
            ->with('course')
            ->get();

        $availableCourses = $this->owned(Course::query())
            ->where('status', 'published')
            ->whereNotIn('id', $instructor->courses->pluck('id'))
            ->orderBy('title')
            ->get();

        $availableBatches = Batch::whereIn('course_id', $ownedCourseIds)
            ->where(function ($q) use ($instructor) {
                $q->whereNull('instructor_id')->orWhere('instructor_id', '!=', $instructor->id);
            })
            ->with('course')
            ->orderBy('title')
            ->get();

        return view('admin.instructors.show', compact('instructor', 'assignedBatches', 'availableCourses', 'availableBatches'));
    }

    public function edit(User $instructor)
    {
        $this->authorizeOwner($instructor);

        return view('admin.instructors.edit', compact('instructor'));
    }

    public function update(Request $request, User $instructor)
    {
        $this->authorizeOwner($instructor);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $instructor->id],
            'phone' => ['nullable', 'string'],
            'expertise' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $instructor->update($validated);

        return redirect()->route('admin.instructors.show', $instructor)->with('success', 'Instructor updated.');
    }

    public function destroy(User $instructor)
    {
        $this->authorizeOwner($instructor);
        $instructor->delete();

        return redirect()->route('admin.instructors.index')->with('success', 'Instructor deleted.');
    }

    public function assignCourse(Request $request, User $instructor)
    {
        $this->authorizeOwner($instructor);

        $validated = $request->validate([
            'course_id' => ['required', Rule::in($this->ownedCourseIds())],
        ]);

        $instructor->courses()->syncWithoutDetaching([$validated['course_id'] => ['is_primary' => false]]);

        return back()->with('success', 'Course assigned to instructor.');
    }

    public function removeCourse(User $instructor, Course $course)
    {
        $this->authorizeOwner($instructor);

        $instructor->courses()->detach($course->id);

        return back()->with('success', 'Course removed from instructor.');
    }

    public function assignBatch(Request $request, User $instructor)
    {
        $this->authorizeOwner($instructor);

        $validated = $request->validate([
            'batch_id' => ['required', Rule::in($this->ownedBatchIds())],
        ]);

        Batch::where('id', $validated['batch_id'])->update(['instructor_id' => $instructor->id]);

        return back()->with('success', 'Batch assigned to instructor.');
    }
}
