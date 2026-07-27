@extends('layouts.app')

@section('title', 'Live Class Attendance')
@section('page-title', 'Live Class Attendance Report')
@section('breadcrumb', 'Reports / Live Class Attendance')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search by class title..." :showDateRange="true" />

    <x-admin.report-datatable table-id="liveClassAttendanceReportTable" :has-records="$events->count() > 0" entity="live classes" :order-column="2" order-direction="desc" export-file-name="live-class-attendance-report" empty-title="No live classes scheduled">
        <thead><tr class="text-left">
            <th>Class Title</th><th>Course / Batch</th><th>Scheduled Start</th><th>Scheduled End</th><th>Learner Attendance</th><th>Status</th>
        </tr></thead>
        <tbody>
            @foreach($events as $event)
            <tr>
                <td class="font-medium text-slate-800">{{ $event->title }}</td>
                <td>{{ $event->course?->title ?? $event->batch?->title ?? '—' }}</td>
                <td class="text-slate-500" data-order="{{ $event->starts_at?->timestamp ?? 0 }}">{{ $event->starts_at?->format('M d, Y H:i') }}</td>
                <td class="text-slate-500" data-order="{{ $event->ends_at?->timestamp ?? 0 }}">{{ $event->ends_at?->format('M d, Y H:i') }}</td>
                <td>—</td>
                <td><x-badge type="info">{{ ucfirst($event->status ?? 'scheduled') }}</x-badge></td>
            </tr>
            @endforeach
        </tbody>
    </x-admin.report-datatable>
</div>
@endsection
