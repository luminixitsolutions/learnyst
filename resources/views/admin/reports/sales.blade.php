@extends('layouts.app')

@section('title', 'Sales Report')
@section('page-title', 'Order Sales Report')
@section('breadcrumb', 'Reports / Sales / Orders')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search order or customer..." :showDateRange="true" :from="$from" :to="$to" />

    <x-stat-card title="Total Sales" :value="'₹'.number_format($total, 0)" :trend="$from.' to '.$to" />

    <x-admin.report-datatable table-id="salesOrdersReportTable" :has-records="$orders->count() > 0" entity="orders" :order-column="4" order-direction="desc" export-file-name="sales-orders-report" empty-title="No sales in this period">
        <thead><tr class="text-left">
            <th>Order</th><th>Customer</th><th>Email</th><th>Total</th><th>Date</th>
        </tr></thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td><a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600">{{ $order->order_number }}</a></td>
                <td class="text-slate-800">{{ $order->user?->name }}</td>
                <td class="text-slate-500">{{ $order->user?->email }}</td>
                <td class="font-medium" data-order="{{ $order->total }}">₹{{ number_format($order->total, 0) }}</td>
                <td class="text-slate-500" data-order="{{ $order->created_at->timestamp }}">{{ $order->created_at->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </x-admin.report-datatable>
</div>
@endsection
