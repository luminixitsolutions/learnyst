<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $query = Tag::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('visibility')) {
            $query->where('visibility', $request->visibility);
        }

        $sort = $request->get('sort', 'latest');
        $query = match ($sort) {
            'name' => $query->orderBy('name'),
            'oldest' => $query->oldest(),
            default => $query->latest(),
        };

        $tags = $query->paginate(15)->withQueryString();

        return view('admin.tags.index', compact('tags', 'sort'));
    }

    public function create()
    {
        return view('admin.tags.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:60', 'unique:tags,name'],
            'visibility' => ['required', Rule::in(['public', 'private', 'classification'])],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $tag = Tag::create($validated);

        ActivityLogger::log('tag_created', "Tag {$tag->name} created", $tag);

        return redirect()
            ->route('admin.tags.index')
            ->with('success', 'Tag created successfully.');
    }

    public function destroy(Tag $tag)
    {
        $name = $tag->name;
        $tag->delete();

        ActivityLogger::log('tag_deleted', "Tag {$name} deleted");

        return redirect()
            ->route('admin.tags.index')
            ->with('success', 'Tag deleted.');
    }
}
