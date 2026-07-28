@extends('layouts.app')
@section('title', 'Payroll')
@section('page-title', 'Payroll')
@section('breadcrumb', 'HR / Payroll')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.hr.payroll.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @csrf
            <x-form-input label="Year" name="year" type="number" :value="now()->year" required />
            <x-form-input label="Month" name="month" type="number" :value="now()->month" min="1" max="12" required />
            <x-form-input label="Notes" name="notes" />
            <div class="flex items-end"><button class="px-5 py-2.5 rounded-xl panel-btn-primary">Run payroll</button></div>
        </form>
    </div>
    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($runs->count())
        <div class="overflow-x-auto">
            <table id="payrollTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Period</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Slips</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($runs as $run)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $run->periodLabel() }}</td>
                        <td class="px-6 py-4"><x-badge type="info">{{ $run->status }}</x-badge></td>
                        <td class="px-6 py-4 text-slate-600">{{ $run->slips_count }}</td>
                        <td class="px-6 py-4"><a href="{{ route('admin.hr.payroll.show', $run) }}" class="text-emerald-600 text-sm">View</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No payroll runs." description="Run payroll using the form above." />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($runs->count())
    <x-admin.datatable-scripts table-id="payrollTable" entity="payroll runs" :order-column="0" order-direction="desc" :action-column="3" export-file-name="payroll-runs" />
@endif
@endpush
