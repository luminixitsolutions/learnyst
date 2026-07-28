@extends('layouts.app')
@section('title', 'Website Builder')
@section('page-title', 'Website Builder')
@section('breadcrumb', 'Website / Builder')
@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap gap-2 justify-between items-center">
        <p class="text-sm text-slate-600">Build custom pages with reusable blocks for <strong>{{ $company->name }}</strong>.</p>
        <div class="flex gap-2">
            <a href="{{ route('admin.website-builder.menus') }}" class="panel-btn-secondary text-sm">Menus & Footer</a>
            <a href="{{ route('admin.website-builder.seo') }}" class="panel-btn-secondary text-sm">SEO</a>
            <a href="{{ route('admin.website-builder.pages.create') }}" class="panel-btn-primary text-sm">New page</a>
        </div>
    </div>
    <div class="glass-card rounded-2xl overflow-hidden">
        <table class="w-full text-sm panel-table">
            <thead><tr><th class="px-6 py-3 text-left">Page</th><th class="px-6 py-3 text-left">Type</th><th class="px-6 py-3 text-left">Status</th><th class="px-6 py-3 text-left">Blocks</th><th></th></tr></thead>
            <tbody>
            @forelse($pages as $page)
                <tr>
                    <td class="px-6 py-3">
                        <div class="font-medium">{{ $page->title }}</div>
                        <div class="text-xs text-slate-500">/{{ $page->slug }}</div>
                    </td>
                    <td class="px-6 py-3">{{ $page->page_type }}</td>
                    <td class="px-6 py-3"><span class="px-2 py-0.5 rounded-full text-xs {{ $page->status==='published'?'bg-emerald-50 text-emerald-700':'bg-slate-100 text-slate-600' }}">{{ $page->status }}</span></td>
                    <td class="px-6 py-3">{{ $page->blocks_count }}</td>
                    <td class="px-6 py-3 text-right space-x-2">
                        @if($page->status==='published')
                            <a href="{{ route('website.companies.page', ['slug'=>$company->slug,'pageSlug'=>$page->slug]) }}" target="_blank" class="text-slate-600 text-xs font-semibold">Preview</a>
                        @endif
                        <a href="{{ route('admin.website-builder.pages.edit', $page) }}" class="text-indigo-600 text-xs font-semibold">Edit</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-6 py-10 text-center text-slate-500">No pages yet. Create Home, About, Contact, or custom landing pages.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
