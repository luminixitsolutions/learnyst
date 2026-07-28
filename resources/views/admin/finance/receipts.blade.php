@extends('layouts.app')
@section('title', 'Receipts')
@section('page-title', 'Receipts')
@section('breadcrumb', 'Finance / Receipts')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-4">
    <p class="text-sm text-slate-500">Payment acknowledgements (RCPT-…). Tax invoices use existing GST numbering (INV-…) under Sales → GST Invoices.</p>
    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($receipts->count())
        <div class="overflow-x-auto">
            <table id="receiptsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Receipt #</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Payer</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($receipts as $r)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 font-mono text-slate-800">{{ $r->receipt_number }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $r->receipt_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $r->payer_name ?? $r->user?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-emerald-600">₹{{ number_format($r->amount,2) }}</td>
                        <td class="px-6 py-4"><a href="{{ route('admin.finance.receipts.show', $r) }}" class="text-emerald-600 text-sm" target="_blank">Print</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No receipts." description="Sync payments from the finance dashboard." />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($receipts->count())
    <x-admin.datatable-scripts table-id="receiptsTable" entity="receipts" :order-column="1" order-direction="desc" :action-column="4" export-file-name="finance-receipts" />
@endif
@endpush
