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

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($learners->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                    <th class="px-6 py-4">Learner</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Total Sales</th>
                    <th class="px-6 py-4">Lead Visits</th>
                    <th class="px-6 py-4">Signed Up On</th>
                    <th class="px-6 py-4">Billing Address</th>
                    <th class="px-6 py-4">Enrollments</th>
                </tr></thead>
                <tbody>
                    @foreach($learners as $learner)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4"><a href="{{ route('admin.reports.learner-profile', $learner) }}" class="text-indigo-600 font-medium">{{ $learner->name }}</a></td>
                        <td class="px-6 py-4 text-slate-500">{{ $learner->email }}</td>
                        <td class="px-6 py-4 text-indigo-600 font-medium">₹{{ number_format($learner->total_spent ?? 0, 0) }}</td>
                        <td class="px-6 py-4 text-slate-800">{{ $leadCounts[$learner->email] ?? 0 }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $learner->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-slate-500 max-w-xs truncate">{{ $learner->address ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-800">{{ $learner->enrollments_count }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $learners->links() }}</div>
        @else
        <x-empty-state title="No learners found" />
        @endif
    </div>
</div>
@endsection
