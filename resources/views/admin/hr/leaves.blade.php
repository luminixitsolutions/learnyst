@extends('layouts.app')
@section('title', 'Leave Requests')
@section('page-title', 'Leave')
@section('breadcrumb', 'HR / Leave')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.hr.leaves.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <x-form-input label="Employee" name="employee_id" type="select" required>
                @foreach($employees as $e)<option value="{{ $e->id }}">{{ $e->name }}</option>@endforeach
            </x-form-input>
            <x-form-input label="Type" name="leave_type" type="select">
                <option value="casual">Casual</option>
                <option value="sick">Sick</option>
                <option value="earned">Earned</option>
            </x-form-input>
            <x-form-input label="From" name="from_date" type="date" required />
            <x-form-input label="To" name="to_date" type="date" required />
            <x-form-input label="Reason" name="reason" type="textarea" class="md:col-span-2" />
            <div class="md:col-span-3"><button class="px-5 py-2.5 rounded-xl panel-btn-primary">Submit</button></div>
        </form>
    </div>
    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($leaves->count())
        <div class="overflow-x-auto">
            <table id="leavesTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Employee</th>
                        <th class="px-6 py-4">Dates</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaves as $leave)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $leave->employee?->name }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $leave->from_date->format('M d') }} – {{ $leave->to_date->format('M d') }}</td>
                        <td class="px-6 py-4"><x-badge type="info">{{ $leave->status }}</x-badge></td>
                        <td class="px-6 py-4 space-x-2">
                            @if($leave->status==='pending')
                            <form method="POST" action="{{ route('admin.hr.leaves.review', $leave) }}" class="inline">@csrf
                                <input type="hidden" name="status" value="approved">
                                <button class="text-emerald-600 text-sm">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.hr.leaves.review', $leave) }}" class="inline">@csrf
                                <input type="hidden" name="status" value="rejected">
                                <button class="text-red-500 text-sm">Reject</button>
                            </form>
                            @else
                            <span class="text-slate-400 text-sm">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No leave requests." description="Submit a leave request using the form above." />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($leaves->count())
    <x-admin.datatable-scripts table-id="leavesTable" entity="leave requests" :order-column="0" order-direction="desc" :action-column="3" export-file-name="leave-requests" />
@endif
@endpush
