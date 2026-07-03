@extends('layouts.app')

@section('title', 'Sales Report')
@section('page-title', 'Sales Report')
@section('breadcrumb', 'Reports / Sales')

@section('content')
<div class="space-y-6">
    <form method="GET" class="flex flex-wrap items-end gap-4 glass-card rounded-2xl p-4">
        <x-form-input label="From" name="from" type="date" :value="$from" />
        <x-form-input label="To" name="to" type="date" :value="$to" />
        <button type="submit" class="px-4 py-2.5 rounded-xl bg-brand-600 text-white text-sm">Apply</button>
        <a href="{{ route('admin.reports.index') }}" class="text-sm text-slate-500 hover:text-white pb-2">← All Reports</a>
    </form>

    <x-stat-card title="Total Sales" :value="'₹'.number_format($total, 0)" :trend="$from.' to '.$to" />

    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                        <th class="px-6 py-4">Order</th>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Total</th>
                        <th class="px-6 py-4">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td class="px-6 py-4"><a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600">{{ $order->order_number }}</a></td>
                        <td class="px-6 py-4 text-white">{{ $order->user?->name }}</td>
                        <td class="px-6 py-4 text-white">₹{{ number_format($order->total, 0) }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $order->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-slate-500">No sales in this period</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
