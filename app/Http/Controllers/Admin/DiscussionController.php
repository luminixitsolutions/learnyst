<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\Discussion;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    use ScopesToCurrentUser;

    protected function ownedDiscussionsQuery()
    {
        $courseIds = $this->ownedCourseIds();
        $communityIds = $this->owned(Community::query())->pluck('id');
        $batchIds = $this->ownedBatchIds();

        return Discussion::query()->where(function ($q) use ($courseIds, $communityIds, $batchIds) {
            $q->whereIn('course_id', $courseIds)
                ->orWhereIn('community_id', $communityIds)
                ->orWhereIn('batch_id', $batchIds);
        });
    }

    protected function authorizeDiscussion(Discussion $discussion): void
    {
        abort_unless($this->ownedDiscussionsQuery()->whereKey($discussion->id)->exists(), 403);
    }

    public function index(Request $request)
    {
        $query = $this->ownedDiscussionsQuery()->with(['user', 'course'])->latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $discussions = $query->get();

        return view('admin.discussions.index', compact('discussions'));
    }

    public function show(Discussion $discussion)
    {
        $this->authorizeDiscussion($discussion);
        $discussion->load(['user', 'course', 'comments.user', 'comments.replies']);

        return view('admin.discussions.show', compact('discussion'));
    }

    public function lock(Discussion $discussion)
    {
        $this->authorizeDiscussion($discussion);
        $discussion->update(['is_locked' => !$discussion->is_locked]);

        return back()->with('success', $discussion->is_locked ? 'Discussion locked.' : 'Discussion unlocked.');
    }

    public function destroy(Discussion $discussion)
    {
        $this->authorizeDiscussion($discussion);
        $discussion->delete();

        return redirect()->route('admin.discussions.index')->with('success', 'Discussion deleted.');
    }
}
