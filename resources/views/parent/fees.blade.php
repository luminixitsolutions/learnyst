@extends('layouts.app')
@section('title', 'Fees')
@section('page-title', 'Fees / Payments')
@section('breadcrumb', 'Parent / Fees')
@section('content')
<div class="space-y-4">
    <div class="glass-card rounded-2xl p-5 flex justify-between items-center">
        <div>
            <p class="text-sm text-slate-500">Outstanding dues (pending / failed)</p>
            <p class="text-2xl font-bold text-slate-800">₹{{ number_format((float) $outstanding, 2) }}</p>
        </div>
        <p class="text-xs text-slate-500">Read-only view of linked learners’ orders</p>
    </div>
    <div class="glass-card rounded-2xl overflow-hidden">
        <table class="w-full text-sm panel-table">
            <thead><tr><th class="px-6 py-3 text-left">Learner</th><th class="px-6 py-3 text-left">Order</th><th class="px-6 py-3 text-left">Total</th><th class="px-6 py-3 text-left">Status</th><th class="px-6 py-3 text-left">Date</th></tr></thead>
            <tbody>
            @forelse($orders as $order)
                <tr>
                    <td class="px-6 py-3">{{ $order->user?->name }}</td>
                    <td class="px-6 py-3">#{{ $order->id }}</td>
                    <td class="px-6 py-3">₹{{ number_format((float) $order->total, 2) }}</td>
                    <td class="px-6 py-3">{{ $order->payment_status }}</td>
                    <td class="px-6 py-3 text-slate-500">{{ $order->created_at?->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">No orders found.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $orders->links() }}</div>
    </div>
</div>
@endsection
