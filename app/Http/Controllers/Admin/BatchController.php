<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Course;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BatchController extends Controller
{
    use ScopesToCurrentUser;

    protected function authorizeBatch(Batch $batch): void
    {
        $batch->loadMissing('course');
        abort_unless($batch->course, 404);
        $this->authorizeOwner($batch->course);
    }

    public function index(Request $request)
    {
        $query = Batch::with(['course', 'instructor'])
            ->whereIn('course_id', $this->ownedCourseIds())
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $batches = $query->get();

        return view('admin.batches.index', compact('batches'));
    }

    public function create()
    {
        $courses = $this->owned(Course::query())->where('status', 'published')->orderBy('title')->get();
        $instructors = $this->ownedUsersQuery('instructor')->get();

        return view('admin.batches.create', compact('courses', 'instructors'));
    }

    public function store(Request $request)
    {
        $ownedInstructorIds = $this->ownedUsersQuery('instructor')->pluck('id');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'course_id' => ['required', Rule::in($this->ownedCourseIds())],
            'instructor_id' => ['nullable', Rule::in($ownedInstructorIds)],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_online' => ['boolean'],
            'template' => ['nullable', 'string'],
            'quiz_type' => ['nullable', 'in:online,offline'],
            'is_free' => ['boolean'],
            'status' => ['required', 'in:upcoming,active,completed,cancelled'],
            'max_learners' => ['nullable', 'integer', 'min:1'],
        ]);

        $validated['is_online'] = $request->boolean('is_online', true);
        $validated['is_free'] = $request->boolean('is_free');
        $batch = Batch::create($validated);

        ActivityLogger::log('batch_created', "Batch {$batch->title} created", $batch);

        return redirect()->route('admin.batches.show', $batch)->with('success', 'Batch created.');
    }

    public function show(Batch $batch)
    {
        $this->authorizeBatch($batch);
        $batch->load(['course', 'instructor', 'learners']);
        $availableLearners = $this->ownedUsersQuery('learner')
            ->whereNotIn('id', $batch->learners->pluck('id'))
            ->orderBy('name')
            ->get();

        return view('admin.batches.show', compact('batch', 'availableLearners'));
    }

    public function edit(Batch $batch)
    {
        $this->authorizeBatch($batch);
        $courses = $this->owned(Course::query())->where('status', 'published')->orderBy('title')->get();
        $instructors = $this->ownedUsersQuery('instructor')->get();

        return view('admin.batches.edit', compact('batch', 'courses', 'instructors'));
    }

    public function update(Request $request, Batch $batch)
    {
        $this->authorizeBatch($batch);
        $ownedInstructorIds = $this->ownedUsersQuery('instructor')->pluck('id');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'course_id' => ['required', Rule::in($this->ownedCourseIds())],
            'instructor_id' => ['nullable', Rule::in($ownedInstructorIds)],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'is_online' => ['boolean'],
            'status' => ['required', 'in:upcoming,active,completed,cancelled'],
        ]);

        $validated['is_online'] = $request->boolean('is_online', true);
        $batch->update($validated);

        return redirect()->route('admin.batches.show', $batch)->with('success', 'Batch updated.');
    }

    public function destroy(Batch $batch)
    {
        $this->authorizeBatch($batch);
        $batch->delete();

        return redirect()->route('admin.batches.index')->with('success', 'Batch deleted.');
    }

    public function addLearner(Request $request, Batch $batch)
    {
        $this->authorizeBatch($batch);

        $validated = $request->validate([
            'user_id' => ['required', Rule::in($this->ownedUsersQuery('learner')->pluck('id'))],
        ]);
        $batch->learners()->syncWithoutDetaching([$validated['user_id'] => ['status' => 'active']]);

        return back()->with('success', 'Learner added to batch.');
    }

    public function removeLearner(Batch $batch, User $user)
    {
        $this->authorizeBatch($batch);
        $this->authorizeOwner($user);
        $batch->learners()->detach($user->id);

        return back()->with('success', 'Learner removed from batch.');
    }
}
