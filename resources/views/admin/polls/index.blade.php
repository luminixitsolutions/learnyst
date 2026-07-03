@extends('layouts.app')

@section('title', 'Polls')
@section('page-title', 'Polls')
@section('breadcrumb', 'Products')

@section('content')
<div class="space-y-6">
    <div class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800">
        Polls created here can be used in SuperLive to boost live class engagement. To enable SuperLive, contact your account manager.
    </div>

    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
        <div>
            <h3 class="text-2xl font-bold text-slate-900">Polls</h3>
            <p class="text-sm text-slate-500 mt-1">Create and manage polls to engage your audience.</p>
        </div>
        <a href="{{ route('admin.polls.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition shadow-sm shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create
        </a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="border-b border-slate-200 px-4 sm:px-6">
            <nav class="flex flex-wrap gap-6 -mb-px">
                @foreach(['all' => 'ALL', 'draft' => 'DRAFT', 'published' => 'PUBLISHED', 'unpublished' => 'UNPUBLISHED'] as $key => $label)
                    <a href="{{ route('admin.polls.index', array_merge(request()->except('page'), ['status' => $key])) }}"
                       class="py-4 text-xs font-bold tracking-wide border-b-2 transition {{ ($status ?? 'all') === $key ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                        {{ $label }}
                        <span class="ml-1 text-slate-400 font-semibold">({{ $statusCounts[$key] ?? 0 }})</span>
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="px-4 sm:px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <form method="GET" class="flex flex-wrap items-center gap-2">
                <input type="hidden" name="status" value="{{ $status ?? 'all' }}">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search polls..."
                       class="px-3 py-2 rounded-lg bg-white border border-slate-200 text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/50 min-w-[200px]">
                <button type="submit" class="panel-btn-secondary text-sm py-2">Search</button>
            </form>
            <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500">
                <span class="px-3 py-2 rounded-lg border border-slate-200 bg-slate-50">Filters</span>
                <span class="px-3 py-2 rounded-lg border border-slate-200 bg-slate-50">Export</span>
            </div>
        </div>

        @if($polls->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4 font-medium">Title</th>
                        <th class="px-6 py-4 font-medium">Poll Type</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Created</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($polls as $poll)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4">
                            <p class="text-slate-800 font-semibold">{{ $poll->title }}</p>
                            @if($poll->description)
                                <p class="text-xs text-slate-500 truncate max-w-xs">{{ $poll->description }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $poll->pollTypeLabel() }}</td>
                        <td class="px-6 py-4">
                            <x-badge :type="match($poll->status) { 'published' => 'success', 'draft' => 'warning', default => 'default' }">
                                {{ ucfirst($poll->status) }}
                            </x-badge>
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $poll->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <form method="POST" action="{{ route('admin.polls.destroy', $poll) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-red-500 hover:text-red-700 text-sm">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $polls->links() }}</div>
        @else
        <div class="py-16 px-6 text-center">
            <div class="w-20 h-20 mx-auto rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center mb-5">
                <svg class="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800">Start Creating Polls</h3>
            <p class="text-sm text-slate-500 mt-2 max-w-md mx-auto">Boost engagement with interactive polls. Create your first poll now.</p>
            <a href="{{ route('admin.polls.create') }}" class="inline-flex items-center gap-2 mt-6 px-5 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
