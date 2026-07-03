<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Poll;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PollController extends Controller
{
    public function index(Request $request)
    {
        $query = Poll::with('creator')->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $status = $request->get('status', 'all');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($request->filled('tag')) {
            $query->whereJsonContains('tags', $request->tag);
        }

        $polls = $query->paginate(15)->withQueryString();
        $statusCounts = [
            'all' => Poll::count(),
            'draft' => Poll::where('status', 'draft')->count(),
            'published' => Poll::where('status', 'published')->count(),
            'unpublished' => Poll::where('status', 'unpublished')->count(),
        ];

        return view('admin.polls.index', compact('polls', 'status', 'statusCounts'));
    }

    public function create()
    {
        $pollTypes = config('poll-types', []);

        return view('admin.polls.create', compact('pollTypes'));
    }

    public function store(Request $request)
    {
        $pollTypes = array_keys(config('poll-types', []));

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:64'],
            'poll_type' => ['required', 'string', Rule::in($pollTypes)],
            'description' => ['nullable', 'string', 'max:256'],
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'draft';

        $poll = Poll::create($validated);

        ActivityLogger::log('poll_created', "Poll {$poll->title} created", $poll);

        return redirect()
            ->route('admin.polls.index')
            ->with('success', 'Poll created successfully.');
    }

    public function destroy(Poll $poll)
    {
        $title = $poll->title;
        $poll->delete();

        ActivityLogger::log('poll_deleted', "Poll {$title} deleted");

        return redirect()
            ->route('admin.polls.index')
            ->with('success', 'Poll deleted.');
    }
}
