@extends('layouts.app')

@section('title', 'School Payouts Report')
@section('page-title', 'School Payouts Report')
@section('breadcrumb', 'Reports / School Payouts')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search by transaction id..." :showDateRange="true" />

    <x-admin.report-datatable
        table-id="schoolPayoutsReportTable"
        :has-records="$payouts->count() > 0"
        entity="payouts"
        :order-column="5"
        order-direction="desc"
        export-file-name="school-payouts-report"
        empty-title="No payout records yet"
        empty-description="Payout data will appear here once school payout tracking is configured."
    >
        <thead><tr class="text-left">
            <th>Payout ID</th>
            <th>Transaction ID</th>
            <th>Amount</th>
            <th>Payment Gateway</th>
            <th>Status</th>
            <th>Date</th>
        </tr></thead>
        <tbody>
            @foreach($payouts as $payout)
            <tr>
                <td class="font-medium text-slate-800">{{ $payout->payout_id ?? $payout->id ?? '—' }}</td>
                <td class="font-mono text-xs text-slate-500">{{ $payout->transaction_id ?? '—' }}</td>
                <td class="text-indigo-600" data-order="{{ $payout->amount ?? 0 }}">₹{{ number_format($payout->amount ?? 0, 2) }}</td>
                <td class="capitalize text-slate-500">{{ $payout->gateway ?? '—' }}</td>
                <td><x-badge :type="($payout->status ?? '') === 'completed' ? 'success' : 'warning'">{{ ucfirst($payout->status ?? '—') }}</x-badge></td>
                <td class="text-slate-500">{{ $payout->date ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </x-admin.report-datatable>
</div>
@endsection
