@extends('layouts.app')

@section('title', 'Growth Report')
@section('page-title', 'Growth Report')
@section('breadcrumb', 'Platform Admin / Reports / Growth')

@section('content')
@php
    $plotHeight = 140;
    $institutesMax = max((int) $series['institutes']->max(), 1);
    $usersMax = max((int) $series['users']->max(), 1);
    $ordersMax = max((int) $series['orders']->max(), 1);
    $revenueMax = max((float) $series['revenue']->max(), 1);
@endphp
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">New institutes, users, and orders over time.</p>
        <a href="{{ route('platform.reports.growth.export', request()->query()) }}" class="panel-btn-primary text-sm">Export CSV</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">
        <x-stat-card title="New institutes" :value="number_format($stats['institutes'])" />
        <x-stat-card title="New users" :value="number_format($stats['users'])" />
        <x-stat-card title="New learners" :value="number_format($stats['learners'])" />
        <x-stat-card title="Orders" :value="number_format($stats['orders'])" />
        <x-stat-card title="Revenue" :value="'₹'.number_format($stats['revenue'], 0)" />
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
        <a href="{{ route('platform.reports.growth') }}" class="panel-btn-secondary text-sm">Reset</a>
    </form>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-slate-700">New institutes</h3>
                <span class="text-sm font-semibold text-sky-600">{{ number_format($series['institutes']->sum()) }}</span>
            </div>
            <div class="relative" style="height: 11rem">
                <div class="absolute inset-x-0 bottom-6 top-0 flex items-end gap-0.5">
                    @foreach($series['institutes'] as $day => $count)
                        @php $barPx = $count > 0 ? max((int) round(($count / $institutesMax) * $plotHeight), 8) : 4; @endphp
                        <div class="flex-1 h-full flex flex-col items-center justify-end min-w-0" title="{{ $day }}: {{ $count }}">
                            <div class="w-full rounded-t" style="height: {{ $barPx }}px; {{ $count > 0 ? 'background: linear-gradient(to top, #0369a1, #7dd3fc);' : 'background:#f1f5f9;' }}"></div>
                        </div>
                    @endforeach
                </div>
                <div class="absolute inset-x-0 bottom-0 flex justify-between text-[10px] text-slate-400">
                    <span>{{ $series['institutes']->keys()->first() }}</span>
                    <span>{{ $series['institutes']->keys()->last() }}</span>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-slate-700">New users</h3>
                <span class="text-sm font-semibold text-amber-600">{{ number_format($series['users']->sum()) }}</span>
            </div>
            <div class="relative" style="height: 11rem">
                <div class="absolute inset-x-0 bottom-6 top-0 flex items-end gap-0.5">
                    @foreach($series['users'] as $day => $count)
                        @php $barPx = $count > 0 ? max((int) round(($count / $usersMax) * $plotHeight), 8) : 4; @endphp
                        <div class="flex-1 h-full flex flex-col items-center justify-end min-w-0" title="{{ $day }}: {{ $count }}">
                            <div class="w-full rounded-t" style="height: {{ $barPx }}px; {{ $count > 0 ? 'background: linear-gradient(to top, #a16207, #fde68a);' : 'background:#f1f5f9;' }}"></div>
                        </div>
                    @endforeach
                </div>
                <div class="absolute inset-x-0 bottom-0 flex justify-between text-[10px] text-slate-400">
                    <span>{{ $series['users']->keys()->first() }}</span>
                    <span>{{ $series['users']->keys()->last() }}</span>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-slate-700">Orders</h3>
                <span class="text-sm font-semibold text-slate-700">{{ number_format($series['orders']->sum()) }}</span>
            </div>
            <div class="relative" style="height: 11rem">
                <div class="absolute inset-x-0 bottom-6 top-0 flex items-end gap-0.5">
                    @foreach($series['orders'] as $day => $count)
                        @php $barPx = $count > 0 ? max((int) round(($count / $ordersMax) * $plotHeight), 8) : 4; @endphp
                        <div class="flex-1 h-full flex flex-col items-center justify-end min-w-0" title="{{ $day }}: {{ $count }}">
                            <div class="w-full rounded-t" style="height: {{ $barPx }}px; {{ $count > 0 ? 'background: linear-gradient(to top, #334155, #94a3b8);' : 'background:#f1f5f9;' }}"></div>
                        </div>
                    @endforeach
                </div>
                <div class="absolute inset-x-0 bottom-0 flex justify-between text-[10px] text-slate-400">
                    <span>{{ $series['orders']->keys()->first() }}</span>
                    <span>{{ $series['orders']->keys()->last() }}</span>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-slate-700">Paid revenue</h3>
                <span class="text-sm font-semibold text-teal-700">₹{{ number_format($series['revenue']->sum(), 0) }}</span>
            </div>
            <div class="relative" style="height: 11rem">
                <div class="absolute inset-x-0 bottom-6 top-0 flex items-end gap-0.5">
                    @foreach($series['revenue'] as $day => $amount)
                        @php $barPx = $amount > 0 ? max((int) round(($amount / $revenueMax) * $plotHeight), 8) : 4; @endphp
                        <div class="flex-1 h-full flex flex-col items-center justify-end min-w-0" title="{{ $day }}: ₹{{ number_format($amount, 0) }}">
                            <div class="w-full rounded-t" style="height: {{ $barPx }}px; {{ $amount > 0 ? 'background: var(--theme-gradient, linear-gradient(to top, #0b7970, #7ac4be));' : 'background:#f1f5f9;' }}"></div>
                        </div>
                    @endforeach
                </div>
                <div class="absolute inset-x-0 bottom-0 flex justify-between text-[10px] text-slate-400">
                    <span>{{ $series['revenue']->keys()->first() }}</span>
                    <span>{{ $series['revenue']->keys()->last() }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="text-sm font-semibold text-slate-700">Daily breakdown</h3>
        </div>
        <div class="overflow-x-auto max-h-96">
            <table class="w-full text-sm panel-table">
                <thead class="sticky top-0 bg-white">
                    <tr class="text-left">
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Institutes</th>
                        <th class="px-6 py-3">Users</th>
                        <th class="px-6 py-3">Learners</th>
                        <th class="px-6 py-3">Orders</th>
                        <th class="px-6 py-3">Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tableRows as $row)
                        <tr>
                            <td class="px-6 py-3 whitespace-nowrap">{{ $row['day'] }}</td>
                            <td class="px-6 py-3">{{ number_format($row['institutes']) }}</td>
                            <td class="px-6 py-3">{{ number_format($row['users']) }}</td>
                            <td class="px-6 py-3">{{ number_format($row['learners']) }}</td>
                            <td class="px-6 py-3">{{ number_format($row['orders']) }}</td>
                            <td class="px-6 py-3 font-semibold">₹{{ number_format($row['revenue'], 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
