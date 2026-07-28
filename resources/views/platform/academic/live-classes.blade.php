@extends('layouts.app')

@section('title', 'Live Classes')
@section('page-title', 'Live Classes')
@section('breadcrumb', 'Platform Admin / Academic / Live Classes')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">Scheduled live classes across institutes (today &amp; upcoming).</p>
        <a href="{{ route('platform.academic.live-classes.export', request()->query()) }}" class="panel-btn-primary text-sm">Export CSV</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-stat-card title="Today" :value="number_format($stats['today'])" />
        <x-stat-card title="Upcoming" :value="number_format($stats['upcoming'])" />
        <x-stat-card title="Live now" :value="number_format($stats['live'])" />
        <x-stat-card title="Cancelled" :value="number_format($stats['cancelled'])" />
    </div>

    <div class="flex flex-wrap gap-2">
        @foreach(['upcoming' => 'Upcoming', 'today' => 'Today', 'past' => 'Past', 'all' => 'All'] as $key => $label)
            <a href="{{ route('platform.academic.live-classes', array_merge(request()->except('window', 'page'), ['window' => $key])) }}"
               class="px-3 py-1.5 rounded-xl text-sm font-medium {{ $window === $key ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">{{ $label }}</a>
        @endforeach
    </div>

    <form method="GET" class="glass-card rounded-2xl p-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-3 items-end">
        <input type="hidden" name="window" value="{{ $window }}">
        <div class="xl:col-span-2">
            <label class="block text-xs font-medium text-slate-500 mb-1">Search</label>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Title or course…" class="panel-input w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Institute</label>
            <select name="company_id" class="panel-input w-full">
                <option value="">All</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}" @selected((string) request('company_id') === (string) $company->id)>{{ $company->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
            <select name="status" class="panel-input w-full">
                <option value="">All</option>
                @foreach(['scheduled','live','completed','cancelled'] as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button class="panel-btn-primary text-sm">Filter</button>
            <a href="{{ route('platform.academic.live-classes', ['window' => $window]) }}" class="panel-btn-secondary text-sm">Reset</a>
        </div>
    </form>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($events->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Class</th>
                        <th class="px-6 py-4">Institute</th>
                        <th class="px-6 py-4">Course</th>
                        <th class="px-6 py-4">When</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($events as $event)
                        @php
                            $badge = match ($event->status) {
                                'live' => 'success',
                                'cancelled' => 'danger',
                                'completed' => 'info',
                                default => 'warning',
                            };
                        @endphp
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800">{{ $event->title }}</div>
                                <div class="text-xs text-slate-400">{{ $event->instructor?->name }} · {{ $event->platform ?: '—' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($event->institute)
                                    <a href="{{ route('platform.companies.show', $event->institute) }}" class="text-indigo-600 hover:underline">{{ $event->institute->name }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ $event->course?->title ?? '—' }}</td>
                            <td class="px-6 py-4 text-slate-500 whitespace-nowrap">
                                {{ $event->starts_at?->format('M d, Y H:i') }}
                                @if($event->ends_at)
                                    <div class="text-xs">→ {{ $event->ends_at->format('H:i') }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4"><x-badge :type="$badge">{{ $event->status }}</x-badge></td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('platform.academic.live-classes.show', $event) }}" class="text-xs font-semibold text-indigo-600">Detail</a>
                                    @if($event->institute?->is_active)
                                        <form method="POST" action="{{ route('platform.companies.enter-panel', $event->institute) }}">@csrf
                                            <button class="text-xs font-semibold text-teal-700">Open panel</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t">{{ $events->links() }}</div>
        @else
            <x-empty-state title="No live classes found" />
        @endif
    </div>
</div>
@endsection
