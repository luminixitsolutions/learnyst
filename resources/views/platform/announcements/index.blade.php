@extends('layouts.app')

@section('title', 'Announcements')
@section('page-title', 'Announcements')
@section('breadcrumb', 'Platform Admin / Support / Announcements')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">Global announcements for institutes and institute admins.</p>
        <a href="{{ route('platform.announcements.create') }}" class="panel-btn-primary text-sm">New announcement</a>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-stat-card title="Total" :value="number_format($stats['total'])" />
        <x-stat-card title="Published" :value="number_format($stats['published'])" />
        <x-stat-card title="Scheduled" :value="number_format($stats['scheduled'])" />
        <x-stat-card title="Draft" :value="number_format($stats['draft'])" />
    </div>

    <form method="GET" class="glass-card rounded-2xl p-4 grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-slate-500 mb-1">Search</label>
            <input type="search" name="search" value="{{ request('search') }}" class="panel-input w-full" placeholder="Title or body…">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
            <select name="status" class="panel-input w-full">
                <option value="">All</option>
                @foreach(['draft','scheduled','published','archived'] as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button class="panel-btn-primary text-sm">Filter</button>
            <a href="{{ route('platform.announcements.index') }}" class="panel-btn-secondary text-sm">Reset</a>
        </div>
    </form>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($announcements->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Announcement</th>
                        <th class="px-6 py-4">Audience</th>
                        <th class="px-6 py-4">Schedule</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($announcements as $announcement)
                        @php
                            $badge = match ($announcement->status) {
                                'published' => 'success',
                                'scheduled' => 'info',
                                'archived' => 'default',
                                default => 'warning',
                            };
                        @endphp
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800">{{ $announcement->title }}</div>
                                <div class="text-xs text-slate-400 line-clamp-1">{{ \Illuminate\Support\Str::limit(strip_tags($announcement->body), 80) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div>{{ $announcement->audienceLabel() }}</div>
                                @if($announcement->company)
                                    <div class="text-xs text-slate-400">{{ $announcement->company->name }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500 whitespace-nowrap text-xs">
                                @if($announcement->starts_at)
                                    <div>From {{ $announcement->starts_at->format('M d, Y H:i') }}</div>
                                @endif
                                @if($announcement->ends_at)
                                    <div>Until {{ $announcement->ends_at->format('M d, Y H:i') }}</div>
                                @endif
                                @unless($announcement->starts_at || $announcement->ends_at)
                                    —
                                @endunless
                            </td>
                            <td class="px-6 py-4">
                                <x-badge :type="$badge">{{ $announcement->status }}</x-badge>
                                @if($announcement->isLive())
                                    <div class="text-[10px] text-emerald-600 mt-1 font-medium">Live</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('platform.announcements.edit', $announcement) }}" class="text-xs font-semibold text-indigo-600">Edit</a>
                                    <form method="POST" action="{{ route('platform.announcements.destroy', $announcement) }}" onsubmit="return confirm('Delete this announcement?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-xs font-semibold text-rose-600">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t">{{ $announcements->links() }}</div>
        @else
            <x-empty-state title="No announcements yet" />
        @endif
    </div>
</div>
@endsection
