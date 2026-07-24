<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Course;
use App\Models\ScheduledEvent;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LiveClassController extends Controller
{
    use ScopesToCurrentUser;

    public function index(Request $request)
    {
        $classes = $this->owned(ScheduledEvent::query())
            ->with(['course', 'batch', 'instructor'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest('starts_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.live-classes.index', compact('classes'));
    }

    public function create()
    {
        return view('admin.live-classes.create', [
            'courses' => $this->owned(Course::query())->orderBy('title')->get(),
            'batches' => Batch::whereIn('course_id', $this->ownedCourseIds())->orderBy('title')->get(),
            'instructors' => $this->ownedUsersQuery('instructor')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateClass($request);
        $validated['created_by'] = Auth::id();
        $validated['type'] = 'class';

        $class = ScheduledEvent::create($validated);
        ActivityLogger::log('live_class_created', "Live class {$class->title} created", $class);

        return redirect()->route('admin.live-classes.index')->with('success', 'Live class scheduled.');
    }

    public function edit(ScheduledEvent $liveClass)
    {
        $this->authorizeOwner($liveClass);

        return view('admin.live-classes.edit', [
            'liveClass' => $liveClass,
            'courses' => $this->owned(Course::query())->orderBy('title')->get(),
            'batches' => Batch::whereIn('course_id', $this->ownedCourseIds())->orderBy('title')->get(),
            'instructors' => $this->ownedUsersQuery('instructor')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, ScheduledEvent $liveClass)
    {
        $this->authorizeOwner($liveClass);
        $liveClass->update($this->validateClass($request));
        ActivityLogger::log('live_class_updated', "Live class {$liveClass->title} updated", $liveClass);

        return redirect()->route('admin.live-classes.index')->with('success', 'Live class updated.');
    }

    public function destroy(ScheduledEvent $liveClass)
    {
        $this->authorizeOwner($liveClass);
        ActivityLogger::log('live_class_deleted', "Live class {$liveClass->title} deleted", $liveClass);
        $liveClass->delete();

        return redirect()->route('admin.live-classes.index')->with('success', 'Live class deleted.');
    }

    protected function validateClass(Request $request): array
    {
        $ownedCourseIds = $this->ownedCourseIds()->all();
        $ownedBatchIds = $this->ownedBatchIds()->all();
        $ownedInstructorIds = $this->ownedUsersQuery('instructor')->pluck('id')->all();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'course_id' => ['nullable', 'integer', Rule::in($ownedCourseIds)],
            'batch_id' => ['nullable', 'integer', Rule::in($ownedBatchIds)],
            'instructor_id' => ['nullable', 'integer', Rule::in($ownedInstructorIds)],
            'starts_at' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'meeting_url' => ['nullable', 'url'],
            'platform' => ['required', 'in:zoom,google_meet,youtube,other'],
            'status' => ['required', 'in:scheduled,live,completed,cancelled'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['starts_at'] = $validated['starts_at'] . ' ' . $validated['start_time'] . ':00';
        if (! empty($validated['end_time'])) {
            $validated['ends_at'] = date('Y-m-d', strtotime($validated['starts_at'])) . ' ' . $validated['end_time'] . ':00';
        }
        unset($validated['start_time'], $validated['end_time']);

        return $validated;
    }
}
