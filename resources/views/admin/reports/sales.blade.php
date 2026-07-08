@extends('layouts.app')

@section('title', 'Sales Report')
@section('page-title', 'Order Sales Report')
@section('breadcrumb', 'Reports / Sales / Orders')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search order or customer..." :showDateRange="true" :from="$from" :to="$to" />

    <x-stat-card title="Total Sales" :value="'₹'.number_format($total, 0)" :trend="$from.' to '.$to" />

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($orders->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                    <th class="px-6 py-4">Order</th>
                    <th class="px-6 py-4">Customer</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Total</th>
                    <th class="px-6 py-4">Date</th>
                </tr></thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td class="px-6 py-4"><a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600">{{ $order->order_number }}</a></td>
                        <td class="px-6 py-4 text-slate-800">{{ $order->user?->name }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $order->user?->email }}</td>
                        <td class="px-6 py-4 font-medium">₹{{ number_format($order->total, 0) }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $order->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No sales in this period" />
        @endif
    </div>
</div>
@endsection
