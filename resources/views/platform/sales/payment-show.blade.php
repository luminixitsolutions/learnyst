@extends('layouts.app')

@section('title', 'Payment #'.$payment->id)
@section('page-title', 'Payment detail')
@section('breadcrumb', 'Platform Admin / Sales / Payment')

@section('content')
<div class="max-w-3xl space-y-6">
    <a href="{{ route('platform.sales.payments') }}" class="text-sm text-slate-500 hover:text-slate-800">← Payments</a>

    <div class="glass-card rounded-2xl p-6 space-y-4 text-sm">
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><dt class="text-slate-500">Transaction</dt><dd class="font-medium">{{ $payment->transaction_id ?: '—' }}</dd></div>
            <div><dt class="text-slate-500">Gateway</dt><dd class="font-medium">{{ $payment->gateway ?: '—' }}</dd></div>
            <div><dt class="text-slate-500">Amount</dt><dd class="font-semibold text-lg">₹{{ number_format((float) $payment->amount, 2) }}</dd></div>
            <div><dt class="text-slate-500">Status</dt><dd><x-badge type="info">{{ $payment->status }}</x-badge></dd></div>
            <div><dt class="text-slate-500">Customer</dt><dd class="font-medium">{{ $payment->user?->email ?? $payment->order?->user?->email ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">Institute</dt><dd class="font-medium">
                @if($institute)
                    <a href="{{ route('platform.companies.show', $institute) }}" class="text-indigo-600 hover:underline">{{ $institute->name }}</a>
                @else
                    —
                @endif
            </dd></div>
            <div><dt class="text-slate-500">Order</dt><dd>
                @if($payment->order)
                    <a href="{{ route('platform.sales.orders.show', $payment->order) }}" class="text-indigo-600 hover:underline">{{ $payment->order->order_number }}</a>
                @else
                    —
                @endif
            </dd></div>
            <div><dt class="text-slate-500">Paid at</dt><dd>{{ $payment->paid_at?->format('M d, Y H:i') ?? '—' }}</dd></div>
        </dl>

        @if($payment->gateway_response)
            <div>
                <h4 class="font-semibold text-slate-800 mb-2">Gateway response</h4>
                <pre class="rounded-xl bg-slate-900 text-slate-100 text-xs p-4 overflow-x-auto">{{ json_encode($payment->gateway_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        @endif

        @if($institute?->is_active)
            <form method="POST" action="{{ route('platform.companies.enter-panel', $institute) }}">@csrf
                <button class="panel-btn-primary text-sm">Open institute panel</button>
            </form>
        @endif
    </div>
</div>
@endsection
