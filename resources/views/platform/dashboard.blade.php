@extends('layouts.app')

@section('title', 'Command Center')
@section('page-title', 'Command Center')
@section('breadcrumb', 'Platform Admin / Overview')

@section('content')
@php
    $plotHeight = 140;
    $revenueMax = max((float) $revenueTrend->max(), 1);
    $institutesMax = max((int) $institutesTrend->max(), 1);
    $learnersMax = max((int) $learnersTrend->max(), 1);
    $roleBadges = [
        'super-admin' => 'danger',
        'admin' => 'success',
        'sub-admin' => 'info',
        'instructor' => 'info',
        'learner' => 'success',
        'counselor' => 'warning',
        'alumni' => 'info',
        'parent' => 'warning',
    ];
@endphp

<div class="space-y-6">
    {{-- Primary KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-stat-card
            title="Institutes"
            :value="number_format($stats['institutes_total'])"
            :trend="number_format($stats['institutes_public']).' public · '.number_format($stats['institutes_hidden']).' hidden'"
            :href="route('platform.companies.index')"
        />
        <x-stat-card
            title="Platform Users"
            :value="number_format($stats['users_total'])"
            :trend="number_format($stats['active_learners']).' active learners'"
            :href="route('platform.users.index')"
        />
        <x-stat-card
            title="Courses & Enrollments"
            :value="number_format($stats['courses_total'])"
            :trend="number_format($stats['enrollments_total']).' enrollments'"
        />
        <x-stat-card
            title="Platform Revenue"
            :value="'₹'.number_format($stats['platform_revenue'], 0)"
            trend="Paid orders (all time)"
        />
    </div>

    {{-- Today strip --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-stat-card title="Today’s Logins" :value="number_format($stats['today_logins'])" />
        <x-stat-card title="Today’s Orders" :value="number_format($stats['today_orders'])" />
        <x-stat-card
            title="Failed Payments"
            :value="number_format($stats['failed_payments'])"
            :trend="$stats['failed_payments_today'] ? number_format($stats['failed_payments_today']).' today' : 'None today'"
        />
    </div>

    {{-- Users by role --}}
    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Users by role</h3>
        <div class="flex flex-wrap gap-3">
            @forelse($stats['users_by_role'] as $slug => $role)
                <div class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                    <x-badge :type="$roleBadges[$slug] ?? 'info'">{{ $role['name'] }}</x-badge>
                    <span class="text-sm font-semibold text-slate-800">{{ number_format($role['count']) }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-500">No roles found.</p>
            @endforelse
        </div>
    </div>

    {{-- Trends --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-800">Revenue · 30 days</h3>
                <span class="text-sm font-semibold text-indigo-600">₹{{ number_format($revenueTrend->sum(), 0) }}</span>
            </div>
            <div class="relative" style="height: 11rem">
                <div class="absolute inset-x-0 bottom-6 top-0 flex items-end gap-0.5">
                    @foreach($revenueTrend as $day => $amount)
                        @php $barPx = $amount > 0 ? max((int) round(($amount / $revenueMax) * $plotHeight), 8) : 4; @endphp
                        <div class="flex-1 h-full flex flex-col items-center justify-end min-w-0" title="{{ $day }}: ₹{{ number_format($amount, 0) }}">
                            <div class="w-full rounded-t transition-all"
                                 style="height: {{ $barPx }}px; {{ $amount > 0 ? 'background: var(--theme-gradient, linear-gradient(to top, #0b7970, #7ac4be));' : 'background:#f1f5f9;' }}"></div>
                        </div>
                    @endforeach
                </div>
                <div class="absolute inset-x-0 bottom-0 flex justify-between border-t border-slate-100 pt-2 text-[10px] text-slate-400">
                    <span>{{ $revenueTrend->keys()->first() }}</span>
                    <span>{{ $revenueTrend->keys()->last() }}</span>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-800">New institutes · 30 days</h3>
                <span class="text-sm font-semibold text-indigo-600">{{ number_format($institutesTrend->sum()) }}</span>
            </div>
            <div class="relative" style="height: 11rem">
                <div class="absolute inset-x-0 bottom-6 top-0 flex items-end gap-0.5">
                    @foreach($institutesTrend as $day => $count)
                        @php $barPx = $count > 0 ? max((int) round(($count / $institutesMax) * $plotHeight), 8) : 4; @endphp
                        <div class="flex-1 h-full flex flex-col items-center justify-end min-w-0" title="{{ $day }}: {{ $count }}">
                            <div class="w-full rounded-t"
                                 style="height: {{ $barPx }}px; {{ $count > 0 ? 'background: linear-gradient(to top, #0369a1, #7dd3fc);' : 'background:#f1f5f9;' }}"></div>
                        </div>
                    @endforeach
                </div>
                <div class="absolute inset-x-0 bottom-0 flex justify-between border-t border-slate-100 pt-2 text-[10px] text-slate-400">
                    <span>{{ $institutesTrend->keys()->first() }}</span>
                    <span>{{ $institutesTrend->keys()->last() }}</span>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-800">New learners · 30 days</h3>
                <span class="text-sm font-semibold text-indigo-600">{{ number_format($learnersTrend->sum()) }}</span>
            </div>
            <div class="relative" style="height: 11rem">
                <div class="absolute inset-x-0 bottom-6 top-0 flex items-end gap-0.5">
                    @foreach($learnersTrend as $day => $count)
                        @php $barPx = $count > 0 ? max((int) round(($count / $learnersMax) * $plotHeight), 8) : 4; @endphp
                        <div class="flex-1 h-full flex flex-col items-center justify-end min-w-0" title="{{ $day }}: {{ $count }}">
                            <div class="w-full rounded-t"
                                 style="height: {{ $barPx }}px; {{ $count > 0 ? 'background: linear-gradient(to top, #a16207, #fde68a);' : 'background:#f1f5f9;' }}"></div>
                        </div>
                    @endforeach
                </div>
                <div class="absolute inset-x-0 bottom-0 flex justify-between border-t border-slate-100 pt-2 text-[10px] text-slate-400">
                    <span>{{ $learnersTrend->keys()->first() }}</span>
                    <span>{{ $learnersTrend->keys()->last() }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Alerts --}}
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Alerts</h3>
            <div class="space-y-3">
                @forelse($alerts as $alert)
                    @php
                        $tone = match ($alert['level'] ?? 'info') {
                            'danger' => 'border-red-200 bg-red-50 text-red-900',
                            'warning' => 'border-amber-200 bg-amber-50 text-amber-900',
                            default => 'border-slate-200 bg-slate-50 text-slate-800',
                        };
                    @endphp
                    <div class="rounded-xl border px-3 py-3 {{ $tone }}">
                        <p class="text-sm font-semibold">{{ $alert['title'] }}</p>
                        <p class="text-xs mt-1 opacity-90">{{ $alert['body'] }}</p>
                        @if(! empty($alert['href']))
                            <a href="{{ $alert['href'] }}" class="inline-block text-xs font-medium mt-2 underline underline-offset-2">Review</a>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No alerts right now. Platform looks healthy.</p>
                @endforelse
            </div>
        </div>

        {{-- Activity + quick links --}}
        <div class="xl:col-span-2 space-y-6">
            <div class="glass-card rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-slate-800">Recent activity</h3>
                    <a href="{{ route('platform.activity.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View all logs →</a>
                </div>
                <div class="space-y-3 max-h-[28rem] overflow-y-auto pr-1">
                    @forelse($recentActivity as $log)
                        <div class="flex items-start justify-between gap-3 py-2 border-b border-slate-100 last:border-0">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-slate-800 truncate">{{ $log->description ?? ucfirst($log->action) }}</p>
                                <p class="text-xs text-slate-500">{{ $log->user?->name ?? 'System' }} · {{ $log->created_at->diffForHumans() }}</p>
                            </div>
                            <x-badge type="info">{{ ucfirst(str_replace('_', ' ', (string) $log->action)) }}</x-badge>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No activity yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="glass-card rounded-2xl p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Quick links</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($quickLinks as $link)
                        <a href="{{ route($link['route']) }}" class="rounded-xl border border-slate-200 px-4 py-3 hover:border-teal-300 hover:bg-teal-50/40 transition group">
                            <p class="text-sm font-semibold text-slate-800 group-hover:text-teal-700">{{ $link['label'] }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $link['hint'] }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
