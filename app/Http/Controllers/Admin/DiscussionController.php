<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discussion;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    public function index(Request $request)
    {
        $query = Discussion::with(['user', 'course'])->latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $discussions = $query->paginate(20)->withQueryString();

        return view('admin.discussions.index', compact('discussions'));
    }

    public function show(Discussion $discussion)
    {
        $discussion->load(['user', 'course', 'comments.user', 'comments.replies']);

        return view('admin.discussions.show', compact('discussion'));
    }

    public function lock(Discussion $discussion)
    {
        $discussion->update(['is_locked' => !$discussion->is_locked]);

        return back()->with('success', $discussion->is_locked ? 'Discussion locked.' : 'Discussion unlocked.');
    }

    public function destroy(Discussion $discussion)
    {
        $discussion->delete();

        return redirect()->route('admin.discussions.index')->with('success', 'Discussion deleted.');
    }
}
