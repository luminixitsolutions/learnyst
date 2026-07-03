@extends('layouts.app')

@section('title', 'Payment #' . $payment->id)
@section('page-title', 'Payment Details')
@section('breadcrumb', 'Payments / #' . $payment->id)

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <x-badge :type="$payment->status === 'success' ? 'success' : 'danger'" class="text-sm">{{ ucfirst($payment->status) }}</x-badge>
            <span class="text-2xl font-bold text-indigo-600">₹{{ number_format($payment->amount, 2) }}</span>
        </div>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div><dt class="text-slate-500">Gateway</dt><dd class="text-white capitalize mt-1">{{ $payment->gateway }}</dd></div>
            <div><dt class="text-slate-500">Transaction ID</dt><dd class="text-white mt-1">{{ $payment->transaction_id ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">Paid At</dt><dd class="text-white mt-1">{{ $payment->paid_at?->format('M d, Y h:i A') ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">Created</dt><dd class="text-white mt-1">{{ $payment->created_at->format('M d, Y h:i A') }}</dd></div>
        </dl>
    </div>

    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Customer</h3>
        <p class="text-slate-800 font-semibold">{{ $payment->user?->name }}</p>
        <p class="text-sm text-slate-500">{{ $payment->user?->email }}</p>
    </div>

    @if($payment->order)
    <div class="glass-card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-800">Related Order</h3>
            <a href="{{ route('admin.orders.show', $payment->order) }}" class="text-indigo-600 text-sm">{{ $payment->order->order_number }}</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-slate-500 text-left"><th class="pb-2">Item</th><th class="pb-2">Total</th></tr></thead>
                <tbody>
                    @foreach($payment->order->items as $item)
                    <tr><td class="py-2 text-white">{{ $item->course?->title }}</td><td class="py-2">₹{{ number_format($item->total, 2) }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <a href="{{ route('admin.payments.index') }}" class="inline-block text-sm text-slate-500 hover:text-white">← Back to payments</a>
</div>
@endsection
