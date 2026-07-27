@extends('layouts.app')

@section('title', 'Courses Report')
@section('page-title', 'Course Report')
@section('breadcrumb', 'Reports / Courses')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search course title...">
        <x-slot:filters>
            <select name="status" class="px-3 py-2 rounded-xl border border-slate-200 text-sm">
                <option value="">All Status</option>
                @foreach(['published','draft','archived'] as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
        </x-slot:filters>
    </x-report-toolbar>

    <x-admin.report-datatable table-id="coursesReportTable" :has-records="$courses->count() > 0" entity="courses" :order-column="1" order-direction="desc" export-file-name="courses-report" empty-title="No courses found">
        <thead><tr class="text-left">
            <th>Course</th><th>Enrollments</th><th>Avg Progress</th><th>Status</th>
        </tr></thead>
        <tbody>
            @foreach($courses as $course)
            <tr>
                <td><a href="{{ route('admin.courses.show', $course) }}" class="text-indigo-600">{{ $course->title }}</a></td>
                <td data-order="{{ $course->enrollments_count }}">{{ $course->enrollments_count }}</td>
                <td data-order="{{ round($course->avg_progress ?? 0) }}">{{ round($course->avg_progress ?? 0) }}%</td>
                <td><x-badge :type="$course->status === 'published' ? 'success' : 'warning'">{{ ucfirst($course->status) }}</x-badge></td>
            </tr>
            @endforeach
        </tbody>
    </x-admin.report-datatable>
</div>
@endsection
