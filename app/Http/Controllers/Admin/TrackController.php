<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\InstructorTrack;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TrackController extends Controller
{
    use ScopesToCurrentUser;

    public function index(Request $request)
    {
        $query = $this->owned(InstructorTrack::query())->with(['instructor', 'creator'])->latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tracks = $query->paginate(15)->withQueryString();

        return view('admin.tracks.index', compact('tracks'));
    }

    public function create()
    {
        $instructors = $this->ownedUsersQuery('instructor')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.tracks.create', compact('instructors'));
    }

    public function store(Request $request)
    {
        $instructorIds = $this->ownedUsersQuery('instructor')->pluck('id')->all();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:60'],
            'description' => ['nullable', 'string', 'max:1000'],
            'instructor_id' => ['required', 'integer', Rule::in($instructorIds)],
            'content_security' => ['required', Rule::in(['encryption', 'no_encryption'])],
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'draft';

        $track = InstructorTrack::create($validated);

        ActivityLogger::log('track_created', "Instructor track {$track->title} created", $track);

        return redirect()
            ->route('admin.tracks.index')
            ->with('success', 'Instructor track created successfully.');
    }

    public function destroy(InstructorTrack $track)
    {
        $this->authorizeOwner($track);
        $title = $track->title;
        $track->delete();

        ActivityLogger::log('track_deleted', "Instructor track {$title} deleted");

        return redirect()
            ->route('admin.tracks.index')
            ->with('success', 'Instructor track deleted.');
    }
}
