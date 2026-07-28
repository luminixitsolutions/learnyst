@extends('layouts.app')

@section('title', 'Institute Performance')
@section('page-title', 'Institute Performance')
@section('breadcrumb', 'Platform Admin / Reports / Performance')

@section('content')
@php
    $plotHeight = 160;
    $chartMax = max((float) ($chartData->max('value') ?: 0), 1);
@endphp
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">Leaderboard by paid revenue in range; learners &amp; courses are current totals.</p>
        <a href="{{ route('platform.reports.performance.export', request()->query()) }}" class="panel-btn-primary text-sm">Export CSV</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-stat-card title="Institutes" :value="number_format($stats['institutes'])" />
        <x-stat-card title="Revenue (range)" :value="'₹'.number_format($stats['revenue'], 0)" />
        <x-stat-card title="Learners" :value="number_format($stats['learners'])" />
        <x-stat-card title="Courses" :value="number_format($stats['courses'])" />
    </div>

    <form method="GET" class="glass-card rounded-2xl p-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">From</label>
            <input type="date" name="from" value="{{ $from->toDateString() }}" class="panel-input">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">To</label>
            <input type="date" name="to" value="{{ $to->toDateString() }}" class="panel-input">
        </div>
        <button class="panel-btn-primary text-sm">Apply</button>
        <a href="{{ route('platform.reports.performance') }}" class="panel-btn-secondary text-sm">Reset</a>
    </form>

    <div class="glass-card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-slate-700">Top institutes by revenue</h3>
            <span class="text-sm font-semibold text-indigo-600">₹{{ number_format($chartData->sum('value'), 0) }}</span>
        </div>
        @if($chartData->contains(fn ($p) => $p->value > 0))
            <div class="relative" style="height: 12rem">
                <div class="absolute inset-x-0 bottom-8 top-0 flex items-end gap-1">
                    @foreach($chartData as $point)
                        @php $barPx = $point->value > 0 ? max((int) round(($point->value / $chartMax) * $plotHeight), 8) : 4; @endphp
                        <div class="flex-1 h-full flex flex-col items-center justify-end min-w-0" title="{{ $point->label }}: ₹{{ number_format($point->value, 0) }}">
                            <div class="w-full rounded-t" style="height: {{ $barPx }}px; {{ $point->value > 0 ? 'background: var(--theme-gradient, linear-gradient(to top, #0b7970, #7ac4be));' : 'background:#f1f5f9;' }}"></div>
                        </div>
                    @endforeach
                </div>
                <div class="absolute inset-x-0 bottom-0 flex gap-1 text-[9px] text-slate-400">
                    @foreach($chartData as $point)
                        <div class="flex-1 truncate text-center" title="{{ $point->label }}">{{ \Illuminate\Support\Str::limit($point->label, 10) }}</div>
                    @endforeach
                </div>
            </div>
        @else
            <p class="text-sm text-slate-500 py-8 text-center">No revenue in this range.</p>
        @endif
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($rows->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">#</th>
                        <th class="px-6 py-4">Institute</th>
                        <th class="px-6 py-4">Package</th>
                        <th class="px-6 py-4">Revenue</th>
                        <th class="px-6 py-4">Orders</th>
                        <th class="px-6 py-4">Learners</th>
                        <th class="px-6 py-4">Courses</th>
                        <th class="px-6 py-4">Enrollments</th>
                        <th class="px-6 py-4 text-right">Panel</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $i => $row)
                        @php $company = $row['company']; @endphp
                        <tr>
                            <td class="px-6 py-4 text-slate-400">{{ $i + 1 }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('platform.companies.show', $company) }}" class="font-medium text-indigo-600 hover:underline">{{ $company->name }}</a>
                                <div class="text-xs text-slate-400">{{ $company->is_active ? 'Active' : 'Suspended' }}</div>
                            </td>
                            <td class="px-6 py-4">{{ $company->subscriptionPackage?->name ?? '—' }}</td>
                            <td class="px-6 py-4 font-semibold">₹{{ number_format($row['revenue'], 0) }}</td>
                            <td class="px-6 py-4">{{ number_format($row['orders']) }}</td>
                            <td class="px-6 py-4">{{ number_format($row['learners']) }}</td>
                            <td class="px-6 py-4">{{ number_format($row['courses']) }}</td>
                            <td class="px-6 py-4">{{ number_format($row['enrollments']) }}</td>
                            <td class="px-6 py-4 text-right">
                                @if($company->is_active)
                                    <form method="POST" action="{{ route('platform.companies.enter-panel', $company) }}" class="inline">@csrf
                                        <button class="text-xs font-semibold text-teal-700">Open panel</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <x-empty-state title="No institutes found" />
        @endif
    </div>
</div>
@endsection
