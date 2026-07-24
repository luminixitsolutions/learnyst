@extends('layouts.app')
@section('title', 'Videos')
@section('page-title', 'Videos')
@section('breadcrumb', 'Website / Videos')
@section('content')
<div class="space-y-6 max-w-5xl">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-xl font-bold text-slate-900">Videos</h3>
            <p class="text-sm text-slate-500">YouTube/Vimeo links for your institute profile.</p>
        </div>
        <a href="{{ route('website.companies.show', $company->slug) }}#videos" target="_blank" class="panel-btn-secondary">Preview</a>
    </div>

    <form method="POST" action="{{ route('admin.company-page.videos.store') }}" enctype="multipart/form-data" class="glass-card rounded-2xl p-6 space-y-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-form-input label="Title" name="title" required />
            <x-form-input label="Video URL" name="video_url" required placeholder="https://youtube.com/watch?v=..." />
        </div>
        <x-form-input label="Description" name="description" type="textarea" />
        <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-slate-700">Thumbnail (optional)</label>
            <input type="file" name="thumbnail" accept="image/*" class="panel-input">
        </div>
        <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="is_published" value="1" checked class="rounded border-slate-300"> Published</label>
        <button class="panel-btn-primary" type="submit">Add video</button>
    </form>

    <div class="space-y-3">
        @forelse($items as $item)
            <div class="glass-card rounded-2xl p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <div class="font-semibold text-slate-900">{{ $item->title }}</div>
                    <div class="text-xs text-slate-500 break-all">{{ $item->video_url }}</div>
                    <x-badge :type="$item->is_published ? 'success' : 'warning'">{{ $item->is_published ? 'Published' : 'Hidden' }}</x-badge>
                </div>
                <form method="POST" action="{{ route('admin.company-page.videos.destroy', $item) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')
                    <button class="text-red-600 text-sm">Delete</button>
                </form>
            </div>
        @empty
            <div class="glass-card rounded-2xl p-8 text-center text-slate-500">No videos yet.</div>
        @endforelse
    </div>
    {{ $items->links() }}
</div>
@endsection
