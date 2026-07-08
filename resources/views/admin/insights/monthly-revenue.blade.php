@extends('layouts.app')

@section('title', 'Monthly Revenue')
@section('page-title', 'Monthly Revenue')
@section('breadcrumb', 'Insights / Monthly Revenue')

@section('content')
<div class="space-y-6">
    <x-insight-toolbar
        :backRoute="route('admin.insights.dashboard')"
        :showDateRange="true"
        :showPeriod="true"
        :showReset="true"
        :showExport="true"
        :showColumns="false"
        :showInfo="true"
        infoText="Revenue from paid orders in the selected date range."
        :from="$from"
        :to="$to"
    />

    <p class="text-sm text-slate-500">Data shown from {{ $from }} to {{ $to }}</p>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card title="Total" :value="'₹'.number_format($summary['total'], 0)" />
        <x-stat-card title="Average" :value="'₹'.number_format($summary['average'], 0)" />
        <x-stat-card title="Highest" :value="'₹'.number_format($summary['highest'], 0)" />
        <x-stat-card title="Lowest" :value="'₹'.number_format($summary['lowest'], 0)" />
    </div>

    <div>
        <h3 class="text-lg font-bold text-slate-800 mb-3">Revenue Breakdown</h3>
        @include('admin.insights.partials.chart', ['chartData' => $chartData])
    </div>
</div>
@endsection
