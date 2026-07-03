<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstructorTrack;
use App\Models\Role;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TrackController extends Controller
{
    public function index(Request $request)
    {
        $query = InstructorTrack::with(['instructor', 'creator'])->latest();

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
        $instructorRole = Role::where('slug', 'instructor')->first();
        $instructors = User::where('role_id', $instructorRole?->id)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.tracks.create', compact('instructors'));
    }

    public function store(Request $request)
    {
        $instructorRole = Role::where('slug', 'instructor')->firstOrFail();
        $instructorIds = User::where('role_id', $instructorRole->id)->pluck('id')->all();

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
        $title = $track->title;
        $track->delete();

        ActivityLogger::log('track_deleted', "Instructor track {$title} deleted");

        return redirect()
            ->route('admin.tracks.index')
            ->with('success', 'Instructor track deleted.');
    }
}
