<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Podcast;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PodcastController extends Controller
{
    public function index(Request $request)
    {
        $query = Podcast::with('creator');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sort = $request->get('sort', 'published_date');
        $query = match ($sort) {
            'title' => $query->orderBy('title'),
            'price' => $query->orderBy('price'),
            default => $query->latest(),
        };

        $podcasts = $query->paginate(15)->withQueryString();

        return view('admin.podcasts.index', compact('podcasts', 'sort'));
    }

    public function create()
    {
        return view('admin.podcasts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:60'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'is_free' => ['sometimes', 'boolean'],
            'content_security' => ['required', Rule::in(['encryption', 'no_encryption'])],
        ]);

        $isFree = $request->boolean('is_free');

        if ($isFree) {
            $validated['price'] = 0;
            $validated['is_free'] = true;
        } else {
            $validated['is_free'] = false;
            $request->validate([
                'price' => ['required', 'numeric', 'min:0'],
            ]);
            $validated['price'] = $request->input('price');
        }

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'draft';

        $podcast = Podcast::create($validated);

        ActivityLogger::log('podcast_created', "Podcast {$podcast->title} created", $podcast);

        return redirect()
            ->route('admin.podcasts.index')
            ->with('success', 'Podcast created successfully.');
    }

    public function destroy(Podcast $podcast)
    {
        $title = $podcast->title;
        $podcast->delete();

        ActivityLogger::log('podcast_deleted', "Podcast {$title} deleted");

        return redirect()
            ->route('admin.podcasts.index')
            ->with('success', 'Podcast deleted.');
    }
}
