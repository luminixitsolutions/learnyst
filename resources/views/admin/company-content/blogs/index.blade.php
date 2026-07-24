@extends('layouts.app')
@section('title', 'Blogs')
@section('page-title', 'Institute Blogs')
@section('breadcrumb', 'Website / Blogs')
@section('content')
<div class="space-y-6 max-w-5xl">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-xl font-bold text-slate-900">Blogs</h3>
            <p class="text-sm text-slate-500">Articles shown on your public institute page.</p>
        </div>
        <a href="{{ route('website.companies.show', $company->slug) }}#blogs" target="_blank" class="panel-btn-secondary">Preview</a>
    </div>

    <form method="POST" action="{{ route('admin.company-page.blogs.store') }}" enctype="multipart/form-data" class="glass-card rounded-2xl p-6 space-y-4">
        @csrf
        <x-form-input label="Title" name="title" required />
        <x-form-input label="Excerpt" name="excerpt" type="textarea" />
        <x-form-input label="Body" name="body" type="textarea" />
        <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-slate-700">Cover image</label>
            <input type="file" name="cover_image" accept="image/*" class="panel-input">
        </div>
        <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="is_published" value="1" checked class="rounded border-slate-300"> Published</label>
        <button class="panel-btn-primary" type="submit">Publish blog</button>
    </form>

    <div class="space-y-3">
        @forelse($items as $item)
            <div class="glass-card rounded-2xl p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <div class="font-semibold text-slate-900">{{ $item->title }}</div>
                    <div class="text-xs text-slate-500">{{ $item->excerpt }}</div>
                    <a class="text-indigo-600 text-sm" target="_blank" href="{{ route('website.companies.blog', [$company->slug, $item->slug]) }}">View post</a>
                </div>
                <form method="POST" action="{{ route('admin.company-page.blogs.destroy', $item) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')
                    <button class="text-red-600 text-sm">Delete</button>
                </form>
            </div>
        @empty
            <div class="glass-card rounded-2xl p-8 text-center text-slate-500">No blog posts yet.</div>
        @endforelse
    </div>
    {{ $items->links() }}
</div>
@endsection
