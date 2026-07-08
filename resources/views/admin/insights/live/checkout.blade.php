@extends('layouts.app')

@section('title', 'Checkout Insight')
@section('page-title', 'Checkout Insight')
@section('breadcrumb', 'Insights / Live / Checkout')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-2">
        <span class="px-2 py-1 rounded-lg bg-amber-100 text-amber-700 text-xs font-semibold">Upcoming</span>
        <h2 class="text-lg font-bold text-slate-800">Checkout Conversion Analytics</h2>
    </div>

    <x-insight-toolbar :backRoute="route('admin.insights.live.index')" :showDateRange="true" :showReset="true" :showExport="false" :showColumns="false" :from="$from" :to="$to" />

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card title="Checkout Conversion" :value="$stats['conversion'].'%'" />
        <x-stat-card title="Cart Abandonment" :value="number_format($stats['abandoned'])" />
        <x-stat-card title="Payment Success" :value="number_format($stats['paid'])" />
        <x-stat-card title="Payment Failed" :value="number_format($stats['failed'])" />
    </div>

    @if($stats['total'] === 0)
    <x-empty-state title="No results found" description="No checkout activity in the selected date range." />
    @endif
</div>
@endsection
