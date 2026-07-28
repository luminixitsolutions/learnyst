<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Instructor\Concerns\ScopesToInstructor;
use App\Models\LiveClassAttendance;
use App\Models\ScheduledEvent;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LiveClassController extends Controller
{
    use ScopesToInstructor;

    public function index(Request $request)
    {
        $query = $this->assignedEventsQuery()->with(['course', 'batch'])->orderByDesc('starts_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $events = $query->paginate(20)->withQueryString();

        return view('instructor.live-classes.index', compact('events'));
    }

    public function create()
    {
        $courses = $this->assignedCoursesQuery()->orderBy('title')->get();

        return view('instructor.live-classes.form', [
            'event' => new ScheduledEvent(['status' => 'scheduled', 'type' => 'class', 'platform' => 'zoom']),
            'courses' => $courses,
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $courseIds = $this->assignedCourseIds()->all();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'course_id' => ['required', Rule::in($courseIds)],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'meeting_url' => ['nullable', 'url', 'max:500'],
            'platform' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:scheduled,live,completed,cancelled'],
            'recording_url' => ['nullable', 'url', 'max:500'],
        ]);

        $event = ScheduledEvent::create([
            ...$validated,
            'type' => 'class',
            'instructor_id' => Auth::id(),
            'created_by' => $this->instituteOwnerId(),
        ]);

        ActivityLogger::log('instructor_live_class_created', "Live class scheduled: {$event->title}", $event);

        return redirect()->route('instructor.live-classes.show', $event)->with('success', 'Live class scheduled.');
    }

    public function show(ScheduledEvent $event)
    {
        abort_unless($event->type === 'class', 404);
        $this->assertCanAccessEvent($event);

        $event->load(['course', 'batch', 'instructor']);
        $attendance = LiveClassAttendance::with('user')->where('scheduled_event_id', $event->id)->latest('attended_at')->get();
        $learners = collect();
        if ($event->course_id) {
            $learners = \App\Models\User::query()
                ->whereIn('id', \App\Models\CourseEnrollment::where('course_id', $event->course_id)->pluck('user_id'))
                ->orderBy('name')
                ->get(['id', 'name', 'email']);
        }

        return view('instructor.live-classes.show', compact('event', 'attendance', 'learners'));
    }

    public function edit(ScheduledEvent $event)
    {
        abort_unless($event->type === 'class', 404);
        $this->assertCanAccessEvent($event);

        return view('instructor.live-classes.form', [
            'event' => $event,
            'courses' => $this->assignedCoursesQuery()->orderBy('title')->get(),
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, ScheduledEvent $event)
    {
        abort_unless($event->type === 'class', 404);
        $this->assertCanAccessEvent($event);

        $courseIds = $this->assignedCourseIds()->all();
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'course_id' => ['required', Rule::in($courseIds)],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'meeting_url' => ['nullable', 'url', 'max:500'],
            'platform' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:scheduled,live,completed,cancelled'],
            'recording_url' => ['nullable', 'url', 'max:500'],
        ]);

        $event->update($validated);

        ActivityLogger::log('instructor_live_class_updated', "Live class updated: {$event->title}", $event);

        return redirect()->route('instructor.live-classes.show', $event)->with('success', 'Live class updated.');
    }

    public function markAttendance(Request $request, ScheduledEvent $event)
    {
        abort_unless($event->type === 'class', 404);
        $this->assertCanAccessEvent($event);

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        LiveClassAttendance::firstOrCreate(
            [
                'scheduled_event_id' => $event->id,
                'user_id' => $validated['user_id'],
            ],
            [
                'marked_by' => Auth::id(),
                'attended_at' => now(),
            ]
        );

        return back()->with('success', 'Attendance marked.');
    }

    protected function assertCanAccessEvent(ScheduledEvent $event): void
    {
        $ok = (int) $event->instructor_id === $this->instructorId()
            || ($event->course_id && $this->assignedCourseIds()->contains($event->course_id));

        abort_unless($ok, 403);
    }
}
