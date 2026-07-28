<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Instructor\Concerns\ScopesToInstructor;
use App\Models\Discussion;
use App\Models\DiscussionComment;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscussionController extends Controller
{
    use ScopesToInstructor;

    public function index(Request $request)
    {
        $query = $this->assignedDiscussionsQuery()->with(['user', 'course'])->withCount('comments')->latest();

        if ($request->query('status') === 'open') {
            $query->where('is_resolved', false);
        } elseif ($request->query('status') === 'resolved') {
            $query->where('is_resolved', true);
        }

        $discussions = $query->paginate(25)->withQueryString();

        return view('instructor.discussions.index', compact('discussions'));
    }

    public function show(Discussion $discussion)
    {
        abort_unless(
            $discussion->course_id && $this->assignedCourseIds()->contains($discussion->course_id),
            403
        );

        $discussion->load(['user', 'course', 'comments.user']);

        return view('instructor.discussions.show', compact('discussion'));
    }

    public function reply(Request $request, Discussion $discussion)
    {
        abort_unless(
            $discussion->course_id && $this->assignedCourseIds()->contains($discussion->course_id),
            403
        );

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        DiscussionComment::create([
            'discussion_id' => $discussion->id,
            'user_id' => Auth::id(),
            'body' => $validated['body'],
        ]);

        $discussion->increment('replies_count');

        ActivityLogger::log('instructor_discussion_replied', "Replied to discussion #{$discussion->id}", $discussion);

        return back()->with('success', 'Reply posted.');
    }

    public function resolve(Discussion $discussion)
    {
        abort_unless(
            $discussion->course_id && $this->assignedCourseIds()->contains($discussion->course_id),
            403
        );

        $discussion->update([
            'is_resolved' => true,
            'resolved_at' => now(),
            'resolved_by' => Auth::id(),
        ]);

        ActivityLogger::log('instructor_discussion_resolved', "Resolved discussion #{$discussion->id}", $discussion);

        return back()->with('success', 'Marked as resolved.');
    }
}
