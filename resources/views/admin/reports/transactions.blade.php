@extends('layouts.app')

@section('title', 'Transactions Report')
@section('page-title', 'Transactions Report')
@section('breadcrumb', 'Reports / Transactions')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search name, email, order or payment id..." :showDateRange="true" :from="request('from', $from)" :to="request('to', $to)">
        <x-slot:filters>
            <select name="status" class="px-3 py-2 rounded-xl border border-slate-200 text-sm">
                <option value="">All Status</option>
                @foreach(['success','failed','pending'] as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
            <select name="gateway" class="px-3 py-2 rounded-xl border border-slate-200 text-sm">
                <option value="">All Gateways</option>
                @foreach(['razorpay','manual','free'] as $gw)
                    <option value="{{ $gw }}" @selected(request('gateway') === $gw)>{{ ucfirst($gw) }}</option>
                @endforeach
            </select>
        </x-slot:filters>
    </x-report-toolbar>

    <x-admin.report-datatable
        table-id="transactionsReportTable"
        :has-records="$payments->count() > 0"
        entity="transactions"
        :order-column="7"
        order-direction="desc"
        export-file-name="transactions-report"
        empty-title="No transactions found"
    >
        <thead><tr class="text-left">
            <th>Order ID</th>
            <th>Learner</th>
            <th>Product</th>
            <th>Amount</th>
            <th>Payment Mode</th>
            <th>Payment Status</th>
            <th>Transaction ID</th>
            <th>Created Date</th>
        </tr></thead>
        <tbody>
            @foreach($payments as $payment)
            <tr>
                <td><a href="{{ route('admin.orders.show', $payment->order) }}" class="text-indigo-600">{{ $payment->order?->order_number }}</a></td>
                <td class="text-slate-800">{{ $payment->user?->name }}</td>
                <td class="text-slate-500">{{ $payment->order?->items->first()?->course?->title ?? '—' }}</td>
                <td class="text-slate-800" data-order="{{ $payment->amount }}">₹{{ number_format($payment->amount, 2) }}</td>
                <td class="capitalize text-slate-500">{{ $payment->gateway }}</td>
                <td><x-badge :type="$payment->status === 'success' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger')">{{ ucfirst($payment->status) }}</x-badge></td>
                <td class="text-slate-500 font-mono text-xs">{{ $payment->transaction_id ?? '—' }}</td>
                <td class="text-slate-500" data-order="{{ $payment->created_at->timestamp }}">{{ $payment->created_at->format('M d, Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </x-admin.report-datatable>
</div>
@endsection
