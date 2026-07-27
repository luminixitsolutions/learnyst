@extends('layouts.app')

@section('title', 'Payment Gateways Report')
@section('page-title', 'Payment Gateways Report')
@section('breadcrumb', 'Reports / Payment Gateways')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search by payment gateway...">
        <x-slot:filters>
            <select name="status" class="px-3 py-2 rounded-xl border border-slate-200 text-sm">
                <option value="">All Status</option>
                <option value="success" @selected(request('status') === 'success')>Success</option>
                <option value="failed" @selected(request('status') === 'failed')>Failed</option>
            </select>
        </x-slot:filters>
    </x-report-toolbar>

    <x-admin.report-datatable
        table-id="paymentGatewaysReportTable"
        :has-records="$gateways->count() > 0"
        entity="gateways"
        :order-column="4"
        order-direction="desc"
        export-file-name="payment-gateways-report"
        empty-title="No payment gateway data"
    >
        <thead><tr class="text-left">
            <th>Gateway Name</th>
            <th>Updated By</th>
            <th>Configuration Type</th>
            <th>Status</th>
            <th>Transactions</th>
            <th>Total Amount</th>
        </tr></thead>
        <tbody>
            @foreach($gateways as $gateway)
            <tr>
                <td class="font-medium text-slate-800">{{ $gateway->name }}</td>
                <td class="text-slate-500">{{ $gateway->updated_by }}</td>
                <td class="text-slate-500">{{ $gateway->config_type }}</td>
                <td><x-badge type="success">{{ $gateway->status }}</x-badge></td>
                <td class="text-slate-800" data-order="{{ $gateway->transaction_count }}">{{ number_format($gateway->transaction_count) }}</td>
                <td class="text-indigo-600" data-order="{{ $gateway->total_amount ?? 0 }}">₹{{ number_format($gateway->total_amount ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </x-admin.report-datatable>
</div>
@endsection
