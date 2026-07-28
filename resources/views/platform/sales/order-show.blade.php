@extends('layouts.app')

@section('title', $order->order_number)
@section('page-title', $order->order_number)
@section('breadcrumb', 'Platform Admin / Sales / Order')

@section('content')
<div class="space-y-6 max-w-4xl">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('platform.sales.orders') }}" class="text-sm text-slate-500 hover:text-slate-800">← All orders</a>
        @if($institute?->is_active)
            <form method="POST" action="{{ route('platform.companies.enter-panel', $institute) }}">@csrf
                <button class="panel-btn-primary text-sm">Open institute panel</button>
            </form>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-stat-card title="Total" :value="'₹'.number_format((float) $order->total, 2)" />
        <x-stat-card title="Status" :value="ucfirst($order->payment_status)" />
        <x-stat-card title="Institute" :value="$institute?->name ?? '—'" />
    </div>

    <div class="glass-card rounded-2xl p-6 space-y-4">
        <h3 class="text-lg font-bold text-slate-800">Order details</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div><dt class="text-slate-500">Customer</dt><dd class="font-medium">{{ $order->user?->name }} · {{ $order->user?->email }}</dd></div>
            <div><dt class="text-slate-500">Method</dt><dd class="font-medium">{{ $order->payment_method ?: '—' }}</dd></div>
            <div><dt class="text-slate-500">Gateway order</dt><dd class="font-mono text-xs">{{ $order->gateway_order_id ?: '—' }}</dd></div>
            <div><dt class="text-slate-500">Paid at</dt><dd class="font-medium">{{ $order->paid_at?->format('M d, Y H:i') ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">Refund status</dt><dd class="font-medium">{{ $order->refund_status }}</dd></div>
            <div><dt class="text-slate-500">Created</dt><dd class="font-medium">{{ $order->created_at?->format('M d, Y H:i') }}</dd></div>
        </dl>
    </div>

    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Items</h3>
        <div class="space-y-2">
            @forelse($order->items as $item)
                <div class="flex justify-between text-sm py-2 border-b border-slate-100">
                    <span>{{ $item->course?->title ?? ($item->item_type.' #'.$item->id) }}</span>
                    <span class="font-semibold">₹{{ number_format((float) $item->total, 2) }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-500">No items</p>
            @endforelse
        </div>
    </div>

    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Payments</h3>
        @forelse($order->payments as $payment)
            <div class="flex items-center justify-between py-2 border-b border-slate-100 text-sm">
                <div>
                    <a href="{{ route('platform.sales.payments.show', $payment) }}" class="text-indigo-600 font-medium">{{ $payment->transaction_id ?: 'Payment #'.$payment->id }}</a>
                    <div class="text-xs text-slate-400">{{ $payment->gateway }} · {{ $payment->status }}</div>
                </div>
                <span class="font-semibold">₹{{ number_format((float) $payment->amount, 2) }}</span>
            </div>
        @empty
            <p class="text-sm text-slate-500">No payments</p>
        @endforelse
    </div>

    @if($order->gstInvoice)
        <div class="glass-card rounded-2xl p-6 text-sm">
            <h3 class="text-lg font-bold text-slate-800 mb-2">GST Invoice</h3>
            <p>{{ $order->gstInvoice->invoice_number }} · ₹{{ number_format((float) $order->gstInvoice->total, 2) }} · {{ $order->gstInvoice->status }}</p>
        </div>
    @endif
</div>
@endsection
