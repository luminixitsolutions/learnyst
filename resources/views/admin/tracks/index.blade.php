@extends('layouts.app')

@section('title', 'Tracks')
@section('page-title', 'Tracks')
@section('breadcrumb', 'Products')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-900">Instructor Tracks</h3>
            <p class="text-sm text-slate-500 mt-1">Build learning tracks that guide learners through a path.</p>
        </div>
        <a href="{{ route('admin.tracks.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl panel-btn-primary shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Track
        </a>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
        <form method="GET" class="flex flex-wrap items-center gap-3 flex-1">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search tracks..."
                   class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
            <select name="status" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm focus:outline-none">
                <option value="">All Status</option>
                @foreach(['draft', 'published', 'unpublished'] as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
            <button type="submit" class="panel-btn-secondary">Filter</button>
        </form>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($tracks->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4 font-medium">Title</th>
                        <th class="px-6 py-4 font-medium">Instructor</th>
                        <th class="px-6 py-4 font-medium">Content Security</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tracks as $track)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4">
                            <p class="text-slate-800 font-semibold">{{ $track->title }}</p>
                            <p class="text-xs text-slate-500">{{ $track->created_at->format('M d, Y') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-slate-700">{{ $track->instructor?->name ?? '—' }}</p>
                            <p class="text-xs text-slate-500">{{ $track->instructor?->email }}</p>
                        </td>
                        <td class="px-6 py-4">{{ $track->contentSecurityLabel() }}</td>
                        <td class="px-6 py-4">
                            <x-badge :type="match($track->status) { 'published' => 'success', 'draft' => 'warning', default => 'default' }">
                                {{ ucfirst($track->status) }}
                            </x-badge>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form method="POST" action="{{ route('admin.tracks.destroy', $track) }}" class="inline">
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
        <div class="px-6 py-4 border-t border-slate-200">{{ $tracks->links() }}</div>
        @else
        <x-empty-state
            title="No tracks yet"
            description="Create instructor tracks by adding a title and assigning an instructor."
            :action="route('admin.tracks.create')"
            actionLabel="Create Track"
        />
        @endif
    </div>
</div>
@endsection
