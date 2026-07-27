@extends('layouts.app')

@section('title', 'Super Live Lessons Report')
@section('page-title', 'Super Live Lessons Report')
@section('breadcrumb', 'Reports / Super Live Lessons')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search by learner name..." :showDateRange="true" />

    <x-admin.report-datatable table-id="superLiveLessonsReportTable" :has-records="$records->count() > 0" entity="live lesson records" :order-column="0" order-direction="asc" export-file-name="super-live-lessons-report" empty-title="No super live lesson data">
        <thead><tr class="text-left">
            <th>Lesson / Course</th><th>Learner</th><th>Attendance</th><th>Watch Duration</th><th>Join Time</th><th>Leave Time</th><th>Status</th>
        </tr></thead>
        <tbody>
            @foreach($records as $record)
            @php $meta = $record->meta ?? []; @endphp
            <tr>
                <td>{{ $record->course?->title ?? '—' }}</td>
                <td>{{ $record->user?->name }}</td>
                <td>{{ $meta['live_attendance'] ?? 'Present' }}</td>
                <td>{{ $meta['watch_duration'] ?? '—' }}</td>
                <td class="text-slate-500">{{ $meta['join_time'] ?? '—' }}</td>
                <td class="text-slate-500">{{ $meta['leave_time'] ?? '—' }}</td>
                <td><x-badge type="info">{{ ucfirst($record->status) }}</x-badge></td>
            </tr>
            @endforeach
        </tbody>
    </x-admin.report-datatable>
</div>
@endsection
