@extends('layouts.app')
@section('title', 'SEO')
@section('page-title', 'SEO helpers')
@section('breadcrumb', 'Website / Builder / SEO')
@section('content')
<div class="space-y-6">
    <a href="{{ route('admin.website-builder.index') }}" class="text-sm text-slate-500">← Builder</a>
    <div class="glass-card rounded-2xl p-5 text-sm text-slate-600 space-y-2">
        <p><strong>Sitemap hint:</strong> Published pages are available at <code class="text-xs bg-slate-100 px-1 rounded">/companies/{{ $company->slug }}/pages/{slug}</code>.</p>
        <p><strong>Robots:</strong> Keep draft pages unpublished so they stay out of public discovery.</p>
    </div>
    <form method="POST" action="{{ route('admin.website-builder.seo.update') }}" class="space-y-4">
        @csrf @method('PUT')
        @foreach($pages as $page)
            <div class="glass-card rounded-2xl p-5 space-y-3">
                <h3 class="font-semibold text-slate-800">{{ $page->title }} <span class="text-xs text-slate-400">/{{ $page->slug }}</span></h3>
                <div>
                    <label class="text-xs text-slate-500">Meta title</label>
                    <input name="pages[{{ $page->id }}][seo_title]" value="{{ $page->seo_title }}" class="panel-input w-full" />
                </div>
                <div>
                    <label class="text-xs text-slate-500">Meta description</label>
                    <textarea name="pages[{{ $page->id }}][seo_description]" rows="2" class="panel-input w-full">{{ $page->seo_description }}</textarea>
                </div>
            </div>
        @endforeach
        @if($pages->isEmpty())
            <p class="text-sm text-slate-500">Create pages first.</p>
        @else
            <button class="panel-btn-primary">Save SEO</button>
        @endif
    </form>
</div>
@endsection
