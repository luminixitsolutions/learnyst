@extends('layouts.app')

@section('title', 'Landing Pages')
@section('page-title', 'Landing Pages')
@section('breadcrumb', 'Marketing / Landing Pages')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Create landing page</h3>
        <form method="POST" action="{{ route('admin.landing-pages.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            <x-form-input label="Title" name="title" required />
            <x-form-input label="Headline" name="headline" />
            <x-form-input label="CTA text" name="cta_text" :value="'Get started'" />
            <x-form-input label="CTA URL" name="cta_url" placeholder="https://..." />
            <x-form-input label="Body" name="body" type="textarea" class="md:col-span-2" />
            <label class="flex items-center gap-2 md:col-span-2">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" name="is_published" value="1" checked class="rounded border-slate-300 bg-white text-emerald-500">
                <span class="text-sm text-slate-600">Published</span>
            </label>
            <div class="md:col-span-2"><button class="px-5 py-2.5 rounded-xl panel-btn-primary">Create</button></div>
        </form>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($pages->count())
        <div class="overflow-x-auto">
            <table id="landingPagesTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Page</th>
                        <th class="px-6 py-4">Views</th>
                        <th class="px-6 py-4">CTA</th>
                        <th class="px-6 py-4">Leads</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pages as $page)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4">
                            <div class="text-slate-800 font-medium">{{ $page->title }}</div>
                            <a href="{{ url('/lp/'.$page->slug) }}" target="_blank" class="text-xs text-emerald-600">/lp/{{ $page->slug }}</a>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $page->views }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $page->cta_clicks }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $page->leads_captured }}</td>
                        <td class="px-6 py-4"><x-badge :type="$page->is_published ? 'success' : 'warning'">{{ $page->is_published ? 'Live' : 'Draft' }}</x-badge></td>
                        <td class="px-6 py-4 space-x-2">
                            <form method="POST" action="{{ route('admin.landing-pages.toggle', $page) }}" class="inline">@csrf
                                <button class="text-sky-600 text-sm">Toggle</button>
                            </form>
                            <form method="POST" action="{{ route('admin.landing-pages.destroy', $page) }}" class="inline">@csrf @method('DELETE')
                                <button class="text-red-500 text-sm" onclick="return confirm('Delete?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No landing pages." description="Create a landing page using the form above." />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($pages->count())
    <x-admin.datatable-scripts table-id="landingPagesTable" entity="landing pages" :order-column="0" order-direction="desc" :action-column="5" export-file-name="landing-pages" />
@endif
@endpush
