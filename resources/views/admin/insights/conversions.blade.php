@extends('layouts.app')

@section('title', 'Conversions')
@section('page-title', 'Conversions Insight')
@section('breadcrumb', 'Insights / Conversions')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-start gap-3">
        <div class="flex-1 min-w-0">
            <x-insight-toolbar :backRoute="route('admin.insights.dashboard')" :showDateRange="true" :showPeriod="true" :showReset="true" :showColumns="false" :from="$from" :to="$to" />
        </div>
        <button type="button" onclick="window.location.reload()" class="panel-btn-secondary text-sm mt-4">Retry</button>
    </div>

    <x-stat-card title="Paid Enrollments" :value="number_format($paidEnrollments)" trend="{{ $from }} to {{ $to }}" />

    <div>
        <h3 class="text-lg font-bold text-slate-800 mb-3">Paid Enrollments Over Time</h3>
        @include('admin.insights.partials.chart', ['chartData' => $chartData])
    </div>
</div>
@endsection
