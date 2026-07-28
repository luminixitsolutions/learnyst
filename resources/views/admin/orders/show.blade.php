@extends('layouts.app')

@section('title', $order->order_number)
@section('page-title', 'Order ' . $order->order_number)
@section('breadcrumb', 'Orders / Details')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <x-badge :type="match($order->payment_status) { 'paid' => 'success', 'failed' => 'danger', 'refunded' => 'info', default => 'warning' }">{{ ucfirst($order->payment_status) }}</x-badge>
            <p class="text-sm text-slate-500 mt-2">{{ $order->created_at->format('M d, Y h:i A') }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.orders.invoice', $order) }}" class="panel-btn-secondary">Download Invoice</a>
            @if($order->payment_status === 'paid' && ! $order->gstInvoice)
            <form method="POST" action="{{ route('admin.orders.gst-invoice', $order) }}">@csrf
                <button type="submit" class="panel-btn-primary">Generate GST Invoice</button>
            </form>
            @elseif($order->gstInvoice)
            <a href="{{ route('admin.gst-invoices.show', $order->gstInvoice) }}" class="panel-btn-primary">View GST Invoice</a>
            @endif
            @if($order->payment_status === 'paid')
            <form method="POST" action="{{ route('admin.orders.refund', $order) }}">@csrf
                <button type="submit" class="px-4 py-2 rounded-xl bg-red-600/20 text-red-400 text-sm hover:bg-red-600/30" onclick="return confirm('Process refund?')">Refund</button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 glass-card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Order Items</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm panel-table">
                    <thead><tr class="text-slate-500 text-left"><th class="pb-2">Course</th><th class="pb-2">Price</th><th class="pb-2">Discount</th><th class="pb-2">Total</th></tr></thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td class="py-2.5 text-slate-800">{{ $item->course?->title }}</td>
                            <td class="py-2.5 text-slate-500">₹{{ number_format($item->price, 2) }}</td>
                            <td class="py-2.5 text-slate-500">₹{{ number_format($item->discount, 2) }}</td>
                            <td class="py-2.5 text-slate-800">₹{{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <dl class="mt-6 space-y-2 text-sm border-t border-slate-200 pt-4">
                <div class="flex justify-between"><dt class="text-slate-500">Subtotal</dt><dd class="text-slate-800">₹{{ number_format($order->subtotal, 2) }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Discount</dt><dd class="text-slate-800">-₹{{ number_format($order->discount, 2) }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Tax (18%)</dt><dd class="text-slate-800">₹{{ number_format($order->tax, 2) }}</dd></div>
                <div class="flex justify-between font-semibold text-lg"><dt class="text-slate-800">Total</dt><dd class="text-indigo-600">₹{{ number_format($order->total, 2) }}</dd></div>
            </dl>
        </div>

        <div class="space-y-6">
            <div class="glass-card rounded-2xl p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Customer</h3>
                <p class="text-slate-800 font-semibold">{{ $order->user?->name }}</p>
                <p class="text-sm text-slate-500">{{ $order->user?->email }}</p>
                <p class="text-sm text-slate-500">{{ $order->user?->phone }}</p>
            </div>
            <div class="glass-card rounded-2xl p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Payment Info</h3>
                <dl class="space-y-2 text-sm">
                    <div><dt class="text-slate-500">Method</dt><dd class="text-white capitalize">{{ $order->payment_method }}</dd></div>
                    @if($order->coupon)<div><dt class="text-slate-500">Coupon</dt><dd class="text-slate-800">{{ $order->coupon->code }}</dd></div>@endif
                    @if($order->paid_at)<div><dt class="text-slate-500">Paid At</dt><dd class="text-slate-800">{{ $order->paid_at->format('M d, Y h:i A') }}</dd></div>@endif
                </dl>
                @if($order->notes)<p class="text-sm text-slate-500 mt-4 border-t border-slate-200 pt-4">{{ $order->notes }}</p>@endif
            </div>
        </div>
    </div>

    @if($order->payments->count())
    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Payments</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-slate-500 text-left"><th class="pb-2">Gateway</th><th class="pb-2">Amount</th><th class="pb-2">Status</th><th class="pb-2">Date</th><th class="pb-2"></th></tr></thead>
                <tbody>
                    @foreach($order->payments as $payment)
                    <tr>
                        <td class="py-2.5 text-slate-800 capitalize">{{ $payment->gateway }}</td>
                        <td class="py-2.5">₹{{ number_format($payment->amount, 2) }}</td>
                        <td class="py-2.5"><x-badge :type="$payment->status === 'success' ? 'success' : 'danger'">{{ ucfirst($payment->status) }}</x-badge></td>
                        <td class="py-2.5 text-slate-500">{{ $payment->paid_at?->format('M d, Y') ?? $payment->created_at->format('M d, Y') }}</td>
                        <td class="py-2.5"><a href="{{ route('admin.payments.show', $payment) }}" class="text-indigo-600 text-sm">View</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
