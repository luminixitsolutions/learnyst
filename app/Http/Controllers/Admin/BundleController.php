<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\Course;
use App\Models\OrderItem;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BundleController extends Controller
{
    public function index(Request $request)
    {
        $query = Bundle::withCount('courses')->latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bundles = $query->paginate(15)->withQueryString();

        return view('admin.bundles.index', compact('bundles'));
    }

    public function create()
    {
        $courses = Course::where('status', 'published')->orderBy('title')->get();

        return view('admin.bundles.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:draft,published,unpublished'],
            'course_ids' => ['required', 'array', 'min:2'],
            'course_ids.*' => ['exists:courses,id'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('bundles', 'public');
        }

        $validated['created_by'] = Auth::id();
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(4);
        unset($validated['course_ids']);

        $bundle = Bundle::create($validated);
        $bundle->courses()->sync(collect($request->course_ids)->mapWithKeys(fn ($id, $i) => [$id => ['sort_order' => $i + 1]]));

        ActivityLogger::log('bundle_created', "Bundle {$bundle->title} created", $bundle);

        return redirect()->route('admin.bundles.show', $bundle)->with('success', 'Bundle created.');
    }

    public function show(Bundle $bundle)
    {
        $bundle->load(['courses', 'enrollments.user']);

        $salesTotal = OrderItem::whereHas('course', fn ($q) => $q->whereIn('id', $bundle->courses->pluck('id')))->sum('total');

        return view('admin.bundles.show', compact('bundle', 'salesTotal'));
    }

    public function edit(Bundle $bundle)
    {
        $bundle->load('courses');
        $courses = Course::where('status', 'published')->orderBy('title')->get();

        return view('admin.bundles.edit', compact('bundle', 'courses'));
    }

    public function update(Request $request, Bundle $bundle)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:draft,published,unpublished'],
            'course_ids' => ['required', 'array', 'min:2'],
            'course_ids.*' => ['exists:courses,id'],
        ]);

        $bundle->update(collect($validated)->except('course_ids')->toArray());
        $bundle->courses()->sync(collect($request->course_ids)->mapWithKeys(fn ($id, $i) => [$id => ['sort_order' => $i + 1]]));

        return redirect()->route('admin.bundles.show', $bundle)->with('success', 'Bundle updated.');
    }

    public function destroy(Bundle $bundle)
    {
        $bundle->delete();

        return redirect()->route('admin.bundles.index')->with('success', 'Bundle deleted.');
    }
}
