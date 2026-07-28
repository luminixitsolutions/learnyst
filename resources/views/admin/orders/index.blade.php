@extends('layouts.app')

@section('title', 'Orders')
@section('page-title', 'Orders')
@section('breadcrumb', 'Sales & Orders')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Order # or customer..."
                   class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm">
            <select name="payment_status" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm">
                <option value="">All Status</option>
                @foreach(['pending','paid','failed','refunded'] as $st)
                    <option value="{{ $st }}" @selected(request('payment_status') === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm">
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm">
            <button type="submit" class="panel-btn-secondary">Filter</button>
        </form>
        <a href="{{ route('admin.orders.create') }}" class="px-5 py-2.5 rounded-xl panel-btn-primary">Create Order</a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($orders->count())
        <div class="overflow-x-auto">
            <table id="ordersTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Order #</th>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Items</th>
                        <th class="px-6 py-4">Total</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4"><a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600 font-medium">{{ $order->order_number }}</a></td>
                        <td class="px-6 py-4 text-slate-800">{{ $order->user?->name }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $order->items->count() }}</td>
                        <td class="px-6 py-4 text-slate-800">₹{{ number_format($order->total, 0) }}</td>
                        <td class="px-6 py-4"><x-badge :type="match($order->payment_status) { 'paid' => 'success', 'failed' => 'danger', 'refunded' => 'info', default => 'warning' }">{{ ucfirst($order->payment_status) }}</x-badge></td>
                        <td class="px-6 py-4 text-slate-500">{{ $order->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.orders.invoice', $order) }}" class="text-slate-500 hover:text-slate-800 text-sm mr-2">Invoice</a>
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600 text-sm">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No orders yet" :action="route('admin.orders.create')" actionLabel="Create Order" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($orders->count())
    <x-admin.datatable-scripts table-id="ordersTable" entity="orders" :order-column="5" order-direction="desc" :action-column="6" export-file-name="orders" />
@endif
@endpush
