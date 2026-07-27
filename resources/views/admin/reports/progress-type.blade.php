@extends('layouts.app')

@section('title', $reportType['title'])
@section('page-title', $reportType['title'])
@section('breadcrumb', 'Reports / Product Progress / ' . $reportType['title'])

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search by learner or product..." :showDateRange="true">
        <x-slot:filters>
            <select name="course_id" class="px-3 py-2 rounded-xl border border-slate-200 text-sm">
                <option value="">All Products</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" @selected(request('course_id') == $course->id)>{{ $course->title }}</option>
                @endforeach
            </select>
            <select name="learner_id" class="px-3 py-2 rounded-xl border border-slate-200 text-sm">
                <option value="">All Learners</option>
                @foreach($learners as $learner)
                    <option value="{{ $learner->id }}" @selected(request('learner_id') == $learner->id)>{{ $learner->name }}</option>
                @endforeach
            </select>
        </x-slot:filters>
    </x-report-toolbar>

    <x-admin.report-datatable
        table-id="progressTypeReportTable"
        :has-records="$records->count() > 0"
        entity="progress records"
        :order-column="5"
        order-direction="desc"
        :export-file-name="$type . '-progress-report'"
        empty-title="No progress data"
        empty-description="Progress records will appear when learners are enrolled and active."
    >
        <thead><tr class="text-left">
            <th>Product / Course</th>
            <th>Learner</th>
            <th>Progress %</th>
            <th>Completed Lessons</th>
            <th>Total Lessons</th>
            <th>Last Activity</th>
            <th>Status</th>
        </tr></thead>
        <tbody>
            @foreach($records as $record)
            @php $meta = $record->meta ?? []; @endphp
            <tr>
                <td class="text-slate-800">{{ $record->course?->title ?? '—' }}</td>
                <td>{{ $record->user?->name }}</td>
                <td data-order="{{ $record->progress ?? 0 }}">{{ number_format($record->progress ?? 0, 0) }}%</td>
                <td>{{ $meta['completed_lessons'] ?? '—' }}</td>
                <td>{{ $meta['total_lessons'] ?? '—' }}</td>
                <td class="text-slate-500" data-order="{{ $record->updated_at?->timestamp ?? 0 }}">{{ $record->updated_at?->format('M d, Y') }}</td>
                <td><x-badge :type="$record->status === 'active' ? 'success' : 'warning'">{{ ucfirst($record->status) }}</x-badge></td>
            </tr>
            @endforeach
        </tbody>
    </x-admin.report-datatable>
</div>
@endsection
