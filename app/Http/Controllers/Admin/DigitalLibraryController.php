<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Ebook;
use App\Models\LibraryItem;
use App\Models\Resource;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DigitalLibraryController extends Controller
{
    use ScopesToCurrentUser;

    public function index(Request $request)
    {
        $query = $this->owned(LibraryItem::query());
        if ($request->filled('type')) {
            $query->where('item_type', $request->type);
        }
        $items = $query->latest()->paginate(20)->withQueryString();
        $types = LibraryItem::types();
        $courses = $this->owned(Course::query())->orderBy('title')->get(['id', 'title']);

        return view('admin.library.index', compact('items', 'types', 'courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'item_type' => ['required', Rule::in(array_keys(LibraryItem::types()))],
            'description' => ['nullable', 'string'],
            'author' => ['nullable', 'string', 'max:120'],
            'access_mode' => ['required', 'in:public,enrolled,subscription,private'],
            'course_id' => ['nullable', Rule::in($this->ownedCourseIds()->all())],
            'allow_download' => ['boolean'],
            'external_url' => ['nullable', 'url'],
            'file' => ['nullable', 'file', 'max:20480'],
            'cover' => ['nullable', 'image', 'max:2048'],
            'sync_ebook' => ['boolean'],
        ]);

        $filePath = $request->hasFile('file')
            ? $request->file('file')->store('library/files', 'public')
            : null;
        $coverPath = $request->hasFile('cover')
            ? $request->file('cover')->store('library/covers', 'public')
            : null;

        $ebookId = null;
        if ($request->boolean('sync_ebook') && $validated['item_type'] === 'ebook') {
            $ebook = Ebook::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'file_path' => $filePath,
                'cover_path' => $coverPath,
                'price' => 0,
                'is_free' => true,
                'content_security' => 'no_encryption',
                'status' => 'published',
                'allow_download' => $request->boolean('allow_download'),
                'created_by' => Auth::id(),
            ]);
            $ebookId = $ebook->id;
        }

        $item = LibraryItem::create([
            'created_by' => Auth::id(),
            'title' => $validated['title'],
            'item_type' => $validated['item_type'],
            'description' => $validated['description'] ?? null,
            'author' => $validated['author'] ?? null,
            'access_mode' => $validated['access_mode'],
            'course_id' => $validated['course_id'] ?? null,
            'allow_download' => $request->boolean('allow_download'),
            'external_url' => $validated['external_url'] ?? null,
            'file_path' => $filePath,
            'cover_path' => $coverPath,
            'ebook_id' => $ebookId,
            'status' => 'published',
        ]);

        ActivityLogger::log('library_item_created', "Library item {$item->title}", $item);

        return back()->with('success', 'Library item added.');
    }

    public function destroy(LibraryItem $libraryItem)
    {
        $this->authorizeOwner($libraryItem);
        $libraryItem->delete();

        return back()->with('success', 'Item removed.');
    }

    public function importExisting()
    {
        $count = 0;
        foreach ($this->owned(Ebook::query())->get() as $ebook) {
            if (LibraryItem::where('ebook_id', $ebook->id)->exists()) {
                continue;
            }
            LibraryItem::create([
                'created_by' => Auth::id(),
                'title' => $ebook->title,
                'item_type' => 'ebook',
                'description' => $ebook->description ?? null,
                'file_path' => $ebook->file_path ?? null,
                'cover_path' => $ebook->cover_path ?? null,
                'allow_download' => (bool) ($ebook->allow_download ?? false),
                'access_mode' => $ebook->is_free ? 'public' : 'enrolled',
                'ebook_id' => $ebook->id,
                'status' => $ebook->status === 'published' ? 'published' : 'draft',
            ]);
            $count++;
        }

        $resources = Resource::query()
            ->when(
                \Illuminate\Support\Facades\Schema::hasColumn('resources', 'created_by'),
                fn ($q) => $q->where(function ($inner) {
                    $inner->where('created_by', Auth::id())->orWhereNull('created_by');
                })
            )
            ->latest()
            ->limit(100)
            ->get();

        foreach ($resources as $resource) {
            if (LibraryItem::where('resource_id', $resource->id)->exists()) {
                continue;
            }
            LibraryItem::create([
                'created_by' => Auth::id(),
                'title' => $resource->title,
                'item_type' => 'resource',
                'description' => $resource->description,
                'file_path' => $resource->file_path,
                'external_url' => $resource->external_url,
                'allow_download' => (bool) ($resource->allow_download ?? true),
                'access_mode' => 'enrolled',
                'resource_id' => $resource->id,
                'status' => ($resource->status ?? '') === 'published' ? 'published' : 'draft',
            ]);
            $count++;
        }

        return back()->with('success', "Imported {$count} existing ebook/resource item(s).");
    }
}
