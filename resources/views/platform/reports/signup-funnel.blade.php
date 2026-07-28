@extends('layouts.app')

@section('title', 'Signup Funnel')
@section('page-title', 'Signup Funnel')
@section('breadcrumb', 'Platform Admin / Reports / Signup Funnel')

@section('content')
@php
    $owners = max($stats['owners'], 1);
    $funnelStages = [
        ['label' => 'Institute owners', 'value' => $stats['owners']],
        ['label' => 'With onboarding answers', 'value' => $stats['with_onboarding']],
        ['label' => 'Company created', 'value' => $stats['with_company']],
    ];
@endphp
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">Signup wizard answers stored on institute owner accounts (<code class="text-xs">users.notes.onboarding</code>).</p>
        <a href="{{ route('platform.reports.signup-funnel.export', request()->query()) }}" class="panel-btn-primary text-sm">Export CSV</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-stat-card title="Owners" :value="number_format($stats['owners'])" />
        <x-stat-card title="With onboarding" :value="number_format($stats['with_onboarding'])" />
        <x-stat-card title="Without answers" :value="number_format($stats['without_onboarding'])" />
        <x-stat-card title="Completion" :value="$stats['completion_rate'].'%'" />
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
        <a href="{{ route('platform.reports.signup-funnel') }}" class="panel-btn-secondary text-sm">Reset</a>
    </form>

    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">Funnel stages</h3>
        <div class="space-y-3">
            @foreach($funnelStages as $stage)
                @php $pct = $owners > 0 ? round(($stage['value'] / $owners) * 100) : 0; @endphp
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-slate-600">{{ $stage['label'] }}</span>
                        <span class="font-semibold text-slate-800">{{ number_format($stage['value']) }} ({{ $pct }}%)</span>
                    </div>
                    <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full rounded-full" style="width: {{ $pct }}%; background: var(--theme-gradient, linear-gradient(to right, #0b7970, #7ac4be));"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">Question completion</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($questions as $key => $meta)
                @php
                    $count = $steps[$key] ?? 0;
                    $pct = $owners > 0 ? round(($count / $owners) * 100) : 0;
                @endphp
                <div class="rounded-xl border border-slate-100 p-4">
                    <div class="text-xs text-slate-500 mb-1">{{ $meta['label'] }}</div>
                    <div class="text-lg font-bold text-slate-800">{{ number_format($count) }} <span class="text-sm font-medium text-slate-400">{{ $pct }}%</span></div>
                    <div class="mt-2 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full rounded-full bg-indigo-500" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
        @foreach(['business_type' => 'Business type', 'source' => 'Acquisition source', 'goal' => 'Business goal', 'audience' => 'Audience'] as $key => $title)
            <div class="glass-card rounded-2xl p-6">
                <h3 class="text-sm font-semibold text-slate-700 mb-4">{{ $title }}</h3>
                @php $counts = $breakdowns[$key] ?? collect(); @endphp
                @if($counts->count())
                    <div class="space-y-2">
                        @foreach($counts as $value => $count)
                            @php
                                $label = $reports->optionLabel($key, (string) $value);
                                $pct = $stats['with_onboarding'] > 0 ? round(($count / $stats['with_onboarding']) * 100) : 0;
                            @endphp
                            <div class="flex items-center gap-3 text-sm">
                                <div class="flex-1 min-w-0">
                                    <div class="truncate text-slate-700" title="{{ $label }}">{{ $label }}</div>
                                    <div class="mt-1 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                        <div class="h-full rounded-full bg-teal-500" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                                <div class="shrink-0 font-semibold text-slate-800 w-16 text-right">{{ $count }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-slate-500">No answers yet.</p>
                @endif
            </div>
        @endforeach
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="text-sm font-semibold text-slate-700">Recent owners</h3>
        </div>
        @if($rows->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Owner</th>
                        <th class="px-6 py-4">Institute</th>
                        <th class="px-6 py-4">Onboarding</th>
                        <th class="px-6 py-4">Source</th>
                        <th class="px-6 py-4">Signed up</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows->sortByDesc(fn ($r) => $r['user']->created_at)->take(40) as $row)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium">{{ $row['user']->name }}</div>
                                <div class="text-xs text-slate-400">{{ $row['user']->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($row['company'])
                                    <a href="{{ route('platform.companies.show', $row['company']) }}" class="text-indigo-600 hover:underline">{{ $row['company']->name }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <x-badge :type="$row['has_onboarding'] ? 'success' : 'warning'">{{ $row['has_onboarding'] ? 'Yes' : 'No' }}</x-badge>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                @if(!empty($row['onboarding']['source']))
                                    {{ $reports->optionLabel('source', $row['onboarding']['source']) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500 whitespace-nowrap">{{ $row['user']->created_at?->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <x-empty-state title="No signup owners in this range" />
        @endif
    </div>
</div>
@endsection
