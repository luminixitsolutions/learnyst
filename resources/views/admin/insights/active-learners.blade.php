@extends('layouts.app')

@section('title', 'Active Learners')
@section('page-title', 'Active Learners Insight')
@section('breadcrumb', 'Insights / Active Learners')

@section('content')
<div class="space-y-6">
    <x-insight-toolbar :backRoute="route('admin.insights.dashboard')" :showDateRange="true" :showPeriod="true" :showReset="true" :showColumns="false" :from="$from" :to="$to" />

    <p class="text-sm text-slate-500">Data shown from {{ $from }} to {{ $to }}</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <x-stat-card title="Signups" :value="number_format($signups)" />
        <x-stat-card title="Active Learners" :value="number_format($activeCount)" />
    </div>

    <div>
        <h3 class="text-lg font-bold text-slate-800 mb-3">Active Learners Comparison</h3>
        @include('admin.insights.partials.chart', ['chartData' => $chartData])
    </div>
</div>
@endsection
