<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Resource;
use App\Models\ResourceDownload;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function index(Request $request)
    {
        $query = Resource::with('category');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sort = $request->get('sort', 'published_date');
        $query = match ($sort) {
            'title' => $query->orderBy('title'),
            default => $query->latest(),
        };

        $resources = $query->get();

        return view('admin.resources.index', compact('resources', 'sort'));
    }

    public function create()
    {
        return view('admin.resources.create');
    }

    public function store(Request $request)
    {
        if ($request->has('resource_type') || $request->hasFile('file_path')) {
            return $this->storeFull($request);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:60'],
        ]);

        $resource = Resource::create([
            'title' => $validated['title'],
            'resource_type' => 'pdf',
            'status' => 'draft',
        ]);

        ActivityLogger::log('resource_created', "Resource {$resource->title} created", $resource);

        return redirect()
            ->route('admin.resources.edit', $resource)
            ->with('success', 'Free resource created. Add files and details below.');
    }

    protected function storeFull(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'resource_type' => ['required', 'in:pdf,video,link,file'],
            'external_url' => ['nullable', 'url'],
            'file_path' => ['nullable', 'file', 'max:51200'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'status' => ['required', 'in:draft,published,unpublished'],
        ]);

        if ($request->hasFile('file_path')) {
            $validated['file_path'] = $request->file('file_path')->store('resources', 'public');
        }

        $resource = Resource::create($validated);
        ActivityLogger::log('resource_created', "Resource {$resource->title} created", $resource);

        return redirect()->route('admin.resources.index')->with('success', 'Resource created.');
    }

    public function edit(Resource $resource)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.resources.edit', compact('resource', 'categories'));
    }

    public function update(Request $request, Resource $resource)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'resource_type' => ['required', 'in:pdf,video,link,file'],
            'external_url' => ['nullable', 'url'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'status' => ['required', 'in:draft,published,unpublished'],
        ]);

        if ($request->hasFile('file_path')) {
            $validated['file_path'] = $request->file('file_path')->store('resources', 'public');
        }

        $resource->update($validated);

        return redirect()->route('admin.resources.index')->with('success', 'Resource updated.');
    }

    public function destroy(Resource $resource)
    {
        $resource->delete();

        return redirect()->route('admin.resources.index')->with('success', 'Resource deleted.');
    }
}
