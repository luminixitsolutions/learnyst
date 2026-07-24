@extends('layouts.app')
@section('title', 'Gallery')
@section('page-title', 'Gallery')
@section('breadcrumb', 'Website / Gallery')
@section('content')
<div class="space-y-6 max-w-5xl">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-xl font-bold text-slate-900">Gallery</h3>
            <p class="text-sm text-slate-500">Images shown on your public institute profile.</p>
        </div>
        <a href="{{ route('website.companies.show', $company->slug) }}#gallery" target="_blank" class="panel-btn-secondary">Preview</a>
    </div>

    <form method="POST" action="{{ route('admin.company-page.gallery.store') }}" enctype="multipart/form-data" class="glass-card rounded-2xl p-6 space-y-4">
        @csrf
        <x-form-input label="Caption (optional)" name="caption" />
        <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-slate-700">Images</label>
            <input type="file" name="images[]" accept="image/*" multiple required class="panel-input">
        </div>
        <button class="panel-btn-primary" type="submit">Upload</button>
    </form>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        @forelse($items as $item)
            <div class="glass-card rounded-2xl overflow-hidden">
                <img src="{{ $item->imageUrl() }}" alt="" class="h-40 w-full object-cover">
                <div class="p-3 space-y-2">
                    <form method="POST" action="{{ route('admin.company-page.gallery.update', $item) }}" class="space-y-2">@csrf @method('PUT')
                        <input type="text" name="caption" value="{{ $item->caption }}" class="panel-input" placeholder="Caption">
                        <label class="inline-flex items-center gap-2 text-xs"><input type="checkbox" name="is_published" value="1" @checked($item->is_published) class="rounded border-slate-300"> Published</label>
                        <button class="text-indigo-600 text-sm">Save</button>
                    </form>
                    <form method="POST" action="{{ route('admin.company-page.gallery.destroy', $item) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')
                        <button class="text-red-600 text-sm">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full glass-card rounded-2xl p-8 text-center text-slate-500">No gallery images yet.</div>
        @endforelse
    </div>
    {{ $items->links() }}
</div>
@endsection
