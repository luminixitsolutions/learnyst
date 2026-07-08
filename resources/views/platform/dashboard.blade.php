@extends('layouts.app')

@section('title', 'Platform Dashboard')
@section('page-title', 'Platform Dashboard')
@section('breadcrumb', 'Platform Admin / Overview')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">
        <x-stat-card title="Companies" :value="number_format($stats['companies'])" :href="route('platform.companies.index')" />
        <x-stat-card title="Platform Users" :value="number_format($stats['total_users'])" :href="route('platform.users.index')" />
        <x-stat-card title="Total Courses" :value="number_format($stats['total_courses'])" />
        <x-stat-card title="Platform Revenue" :value="'₹'.number_format($stats['platform_revenue'], 0)" />
        <x-stat-card title="Active Learners" :value="number_format($stats['active_learners'])" />
    </div>

    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Recent Platform Activity</h3>
        <div class="space-y-3">
            @forelse($recentActivity as $log)
            <div class="flex items-start justify-between py-2 border-b border-slate-100 last:border-0">
                <div>
                    <p class="text-sm font-medium text-slate-800">{{ $log->description ?? ucfirst($log->action) }}</p>
                    <p class="text-xs text-slate-500">{{ $log->user?->name ?? 'System' }} · {{ $log->created_at->diffForHumans() }}</p>
                </div>
                <x-badge type="info">{{ ucfirst($log->action) }}</x-badge>
            </div>
            @empty
            <p class="text-sm text-slate-500">No activity yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
