@extends('layouts.app')

@section('title', 'Product Time Spent')
@section('page-title', 'Product Time Spent Insight')
@section('breadcrumb', 'Insights / Time Spent')

@section('content')
<div class="space-y-6">
    <x-insight-toolbar :backRoute="route('admin.insights.dashboard')" :showDateRange="true" :showPeriod="true" :showReset="true" :showColumns="false" :from="$from" :to="$to" />

    <p class="text-sm text-slate-500">Data shown from {{ $from }} to {{ $to }}</p>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card title="Average Time Spent" :value="$summary['average'].'%'" />
        <x-stat-card title="Active Learners" :value="number_format($summary['learners'])" />
        <x-stat-card title="Courses" :value="number_format($summary['courses'])" />
        <x-stat-card title="Tests" :value="number_format($summary['tests'])" />
        <x-stat-card title="Test Series" :value="number_format($summary['test_series'])" />
        <x-stat-card title="Bundles" :value="number_format($summary['bundles'])" />
        <x-stat-card title="Newsfeed" :value="number_format($summary['newsfeed'])" />
        <x-stat-card title="Communities" :value="number_format($summary['communities'])" />
    </div>

    <div>
        <h3 class="text-lg font-bold text-slate-800 mb-3">Time Spent</h3>
        @include('admin.insights.partials.chart', ['chartData' => $chartData])
    </div>
</div>
@endsection
