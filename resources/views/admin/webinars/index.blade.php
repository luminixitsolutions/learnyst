@extends('layouts.app')

@section('title', 'Webinars')
@section('page-title', 'Webinars')
@section('breadcrumb', 'More Products / Webinars')

@section('content')
<div class="space-y-6">
    <a href="{{ route('admin.more-products.index') }}" class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back
    </a>

    <p class="text-sm text-slate-500 -mt-2">Welcome to your webinar dashboard</p>

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <form method="GET" class="flex flex-1 max-w-md items-center gap-2">
            <div class="relative flex-1">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search by Title"
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
            </div>
            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
        </form>
        <div class="flex flex-wrap items-center gap-2 shrink-0">
            <button type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm font-medium text-slate-600 hover:bg-slate-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Reorder
            </button>
            <a href="{{ route('admin.webinars.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create
            </a>
        </div>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <span class="px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-xs font-medium text-slate-600 inline-flex items-center gap-2 w-fit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Filters
            </span>
            <form method="GET" class="flex items-center gap-2">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                <select name="sort" onchange="this.form.submit()" class="px-3 py-2 rounded-lg bg-white border border-slate-200 text-sm text-slate-700 focus:outline-none">
                    <option value="published_date" @selected(($sort ?? 'published_date') === 'published_date')>Published Date</option>
                    <option value="title" @selected(($sort ?? '') === 'title')>Title</option>
                    <option value="price" @selected(($sort ?? '') === 'price')>Price</option>
                </select>
            </form>
        </div>

        @if($webinars->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4 font-medium">Title</th>
                        <th class="px-6 py-4 font-medium">Price</th>
                        <th class="px-6 py-4 font-medium">Security</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Created</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($webinars as $webinar)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 font-semibold text-slate-800">{{ $webinar->title }}</td>
                        <td class="px-6 py-4">
                            @if($webinar->is_free)
                                <span class="text-emerald-600 font-medium">Free</span>
                            @else
                                ₹{{ number_format($webinar->price, 0) }}
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $webinar->contentSecurityLabel() }}</td>
                        <td class="px-6 py-4">
                            <x-badge :type="match($webinar->status) { 'published' => 'success', 'draft' => 'warning', default => 'default' }">
                                {{ ucfirst($webinar->status) }}
                            </x-badge>
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $webinar->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <form method="POST" action="{{ route('admin.webinars.destroy', $webinar) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-red-500 hover:text-red-700 text-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $webinars->links() }}</div>
        @else
        <div class="py-20 px-6 text-center">
            <div class="w-24 h-24 mx-auto mb-6 text-slate-300">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    <circle cx="10" cy="10" r="6" stroke-width="1.5"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800">No results found</h3>
            <p class="text-sm text-slate-500 mt-2">Create your first webinar to get started.</p>
            <a href="{{ route('admin.webinars.create') }}" class="inline-flex items-center gap-2 mt-6 px-5 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
