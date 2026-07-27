@extends('layouts.app')

@section('title', 'Enrollments Report')
@section('page-title', 'Learner Enrollments Report')
@section('breadcrumb', 'Reports / Enrollments')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search by email or name..." :showDateRange="true" :from="request('from')" :to="request('to')">
        <x-slot:filters>
            <select name="status" class="px-3 py-2 rounded-xl border border-slate-200 text-sm">
                <option value="">All Status</option>
                @foreach(['active','expired','revoked','completed'] as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
            <select name="type" class="px-3 py-2 rounded-xl border border-slate-200 text-sm">
                <option value="">All Types</option>
                @foreach(['course','batch','bundle'] as $tp)
                    <option value="{{ $tp }}" @selected(request('type') === $tp)>{{ ucfirst($tp) }}</option>
                @endforeach
            </select>
            <select name="course_id" class="px-3 py-2 rounded-xl border border-slate-200 text-sm">
                <option value="">All Products</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" @selected(request('course_id') == $course->id)>{{ $course->title }}</option>
                @endforeach
            </select>
        </x-slot:filters>
    </x-report-toolbar>

    <x-admin.report-datatable
        table-id="enrollmentsReportTable"
        :has-records="$enrollments->count() > 0"
        entity="enrollments"
        :order-column="4"
        order-direction="desc"
        export-file-name="enrollments-report"
        empty-title="No enrollment data"
        empty-description="Try adjusting your search or filters."
    >
        <thead><tr class="text-left">
            <th>Product / Course</th>
            <th>Learner</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>Enrollment Date</th>
            <th>Access Start</th>
            <th>Access Expiry</th>
            <th>Status</th>
        </tr></thead>
        <tbody>
            @foreach($enrollments as $enrollment)
            <tr>
                <td class="text-slate-800">
                    @if($enrollment->enrollment_type === 'course') {{ $enrollment->course?->title ?? '—' }}
                    @elseif($enrollment->enrollment_type === 'batch') {{ $enrollment->batch?->title ?? '—' }}
                    @else {{ $enrollment->bundle?->title ?? '—' }}
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.reports.learner-profile', $enrollment->user) }}" class="text-indigo-600 hover:underline">{{ $enrollment->user?->name }}</a>
                </td>
                <td class="text-slate-500">{{ $enrollment->user?->email }}</td>
                <td class="text-slate-500">{{ $enrollment->user?->phone ?? '—' }}</td>
                <td class="text-slate-500" data-order="{{ $enrollment->enrolled_at?->timestamp ?? 0 }}">{{ $enrollment->enrolled_at?->format('M d, Y') ?? '—' }}</td>
                <td class="text-slate-500">{{ $enrollment->access_starts_at?->format('M d, Y') ?? '—' }}</td>
                <td class="text-slate-500">{{ $enrollment->expires_at?->format('M d, Y') ?? '—' }}</td>
                <td>
                    <x-badge :type="match($enrollment->status) { 'active' => 'success', 'expired' => 'warning', 'revoked' => 'danger', default => 'default' }">{{ ucfirst($enrollment->status) }}</x-badge>
                </td>
            </tr>
            @endforeach
        </tbody>
    </x-admin.report-datatable>
</div>
@endsection
