@extends('layouts.app')

@section('title', 'Landing Pages')
@section('page-title', 'Landing Pages')
@section('breadcrumb', 'Marketing / Landing Pages')

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-white mb-4">Create landing page</h3>
        <form method="POST" action="{{ route('admin.landing-pages.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            <x-form-input label="Title" name="title" required />
            <x-form-input label="Headline" name="headline" />
            <x-form-input label="CTA text" name="cta_text" :value="'Get started'" />
            <x-form-input label="CTA URL" name="cta_url" placeholder="https://..." />
            <x-form-input label="Body" name="body" type="textarea" class="md:col-span-2" />
            <label class="flex items-center gap-2 md:col-span-2">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" name="is_published" value="1" checked class="rounded border-slate-600 bg-slate-800 text-emerald-500">
                <span class="text-sm text-slate-300">Published</span>
            </label>
            <div class="md:col-span-2"><button class="px-5 py-2.5 rounded-xl panel-btn-primary">Create</button></div>
        </form>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        <table class="w-full text-sm panel-table">
            <thead><tr class="text-left">
                <th class="px-6 py-4">Page</th>
                <th class="px-6 py-4">Views</th>
                <th class="px-6 py-4">CTA</th>
                <th class="px-6 py-4">Leads</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4"></th>
            </tr></thead>
            <tbody>
                @forelse($pages as $page)
                <tr>
                    <td class="px-6 py-4">
                        <div class="text-white font-medium">{{ $page->title }}</div>
                        <a href="{{ url('/lp/'.$page->slug) }}" target="_blank" class="text-xs text-emerald-400">/lp/{{ $page->slug }}</a>
                    </td>
                    <td class="px-6 py-4 text-slate-400">{{ $page->views }}</td>
                    <td class="px-6 py-4 text-slate-400">{{ $page->cta_clicks }}</td>
                    <td class="px-6 py-4 text-slate-400">{{ $page->leads_captured }}</td>
                    <td class="px-6 py-4"><x-badge :type="$page->is_published ? 'success' : 'warning'">{{ $page->is_published ? 'Live' : 'Draft' }}</x-badge></td>
                    <td class="px-6 py-4 space-x-2">
                        <form method="POST" action="{{ route('admin.landing-pages.toggle', $page) }}" class="inline">@csrf
                            <button class="text-sky-400 text-sm">Toggle</button>
                        </form>
                        <form method="POST" action="{{ route('admin.landing-pages.destroy', $page) }}" class="inline">@csrf @method('DELETE')
                            <button class="text-red-400 text-sm" onclick="return confirm('Delete?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-slate-500">No landing pages.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $pages->links() }}</div>
    </div>
</div>
@endsection
