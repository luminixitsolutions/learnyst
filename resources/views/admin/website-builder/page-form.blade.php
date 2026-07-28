@extends('layouts.app')
@section('title', 'New page')
@section('page-title', 'Create page')
@section('breadcrumb', 'Website / Builder / New')
@section('content')
<a href="{{ route('admin.website-builder.index') }}" class="text-sm text-slate-500">← Pages</a>
<form method="POST" action="{{ route('admin.website-builder.pages.store') }}" class="glass-card rounded-2xl p-6 space-y-4 mt-4 max-w-2xl">
    @csrf
    <div>
        <label class="text-xs text-slate-500">Title</label>
        <input name="title" value="{{ old('title') }}" required class="panel-input w-full" />
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="text-xs text-slate-500">Slug (optional)</label>
            <input name="slug" value="{{ old('slug') }}" class="panel-input w-full" placeholder="about-us" />
        </div>
        <div>
            <label class="text-xs text-slate-500">Page type</label>
            <select name="page_type" class="panel-input w-full">
                @foreach(['home','about','contact','faq','testimonials','faculty','gallery','blog','custom'] as $t)
                    <option value="{{ $t }}" @selected(old('page_type','custom')===$t)>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div>
        <label class="text-xs text-slate-500">Status</label>
        <select name="status" class="panel-input w-full">
            <option value="draft">Draft</option>
            <option value="published">Published</option>
        </select>
    </div>
    <div>
        <label class="text-xs text-slate-500">SEO title</label>
        <input name="seo_title" value="{{ old('seo_title') }}" class="panel-input w-full" />
    </div>
    <div>
        <label class="text-xs text-slate-500">SEO description</label>
        <textarea name="seo_description" rows="2" class="panel-input w-full">{{ old('seo_description') }}</textarea>
    </div>
    <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="show_in_nav" value="1" checked /> Show in navigation</label>
    <div><button class="panel-btn-primary">Create page</button></div>
</form>
@endsection
