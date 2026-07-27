@extends('layouts.app')

@section('title', 'Learners Report')
@section('page-title', 'Learners Report')
@section('breadcrumb', 'Reports / Learners')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search by email or name...">
        <x-slot:filters>
            <select name="status" class="px-3 py-2 rounded-xl border border-slate-200 text-sm">
                <option value="">All Status</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
            </select>
        </x-slot:filters>
    </x-report-toolbar>

    <x-admin.report-datatable table-id="learnersReportTable" :has-records="$learners->count() > 0" entity="learners" :order-column="2" order-direction="desc" export-file-name="learners-report" empty-title="No learners found">
        <thead><tr class="text-left">
            <th>Learner</th><th>Email</th><th>Total Sales</th><th>Lead Visits</th><th>Signed Up On</th><th>Billing Address</th><th>Enrollments</th>
        </tr></thead>
        <tbody>
            @foreach($learners as $learner)
            <tr>
                <td><a href="{{ route('admin.reports.learner-profile', $learner) }}" class="text-indigo-600 font-medium">{{ $learner->name }}</a></td>
                <td class="text-slate-500">{{ $learner->email }}</td>
                <td class="text-indigo-600 font-medium" data-order="{{ $learner->total_spent ?? 0 }}">₹{{ number_format($learner->total_spent ?? 0, 0) }}</td>
                <td data-order="{{ $leadCounts[$learner->email] ?? 0 }}">{{ $leadCounts[$learner->email] ?? 0 }}</td>
                <td class="text-slate-500" data-order="{{ $learner->created_at->timestamp }}">{{ $learner->created_at->format('M d, Y') }}</td>
                <td class="text-slate-500 max-w-xs truncate">{{ $learner->address ?? '—' }}</td>
                <td data-order="{{ $learner->enrollments_count }}">{{ $learner->enrollments_count }}</td>
            </tr>
            @endforeach
        </tbody>
    </x-admin.report-datatable>
</div>
@endsection
