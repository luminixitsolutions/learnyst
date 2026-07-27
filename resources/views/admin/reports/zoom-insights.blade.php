@extends('layouts.app')

@section('title', 'Zoom Insights Report')
@section('page-title', 'Integrated Zoom Insights')
@section('breadcrumb', 'Reports / Zoom Insights')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search by class or learner..." :showDateRange="true" />

    <x-admin.report-datatable table-id="zoomInsightsReportTable" :has-records="$events->count() > 0" entity="zoom sessions" :order-column="3" order-direction="desc" export-file-name="zoom-insights-report" empty-title="No Zoom sessions found" empty-description="Schedule live classes with Zoom platform to see insights here.">
        <thead><tr class="text-left">
            <th>Zoom Class</th><th>Course</th><th>Instructor</th><th>Start Time</th><th>End Time</th><th>Attendance Status</th>
        </tr></thead>
        <tbody>
            @foreach($events as $event)
            <tr>
                <td class="font-medium text-slate-800">{{ $event->title }}</td>
                <td class="text-slate-500">{{ $event->course?->title ?? '—' }}</td>
                <td>{{ $event->instructor?->name ?? '—' }}</td>
                <td class="text-slate-500" data-order="{{ $event->starts_at?->timestamp ?? 0 }}">{{ $event->starts_at?->format('M d, Y H:i') }}</td>
                <td class="text-slate-500" data-order="{{ $event->ends_at?->timestamp ?? 0 }}">{{ $event->ends_at?->format('M d, Y H:i') }}</td>
                <td class="text-slate-500">{{ ucfirst($event->status ?? 'scheduled') }}</td>
            </tr>
            @endforeach
        </tbody>
    </x-admin.report-datatable>
</div>
@endsection
