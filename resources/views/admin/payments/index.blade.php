@extends('layouts.app')

@section('title', 'Payments')
@section('page-title', 'Payments')
@section('breadcrumb', 'Payment transactions')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <x-stat-card title="Total Received" :value="'₹'.number_format($totalReceived, 0)" />
        <x-stat-card title="Failed Payments" :value="number_format($failedCount)" />
    </div>

    <form method="GET" class="flex flex-wrap items-center gap-3">
        <select name="status" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm">
            <option value="">All Status</option>
            @foreach(['success','failed','pending'] as $st)
                <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
            @endforeach
        </select>
        <select name="gateway" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm">
            <option value="">All Gateways</option>
            @foreach(['razorpay','manual','free'] as $gw)
                <option value="{{ $gw }}" @selected(request('gateway') === $gw)>{{ ucfirst($gw) }}</option>
            @endforeach
        </select>
        <button type="submit" class="panel-btn-secondary">Filter</button>
    </form>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($payments->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Order</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Gateway</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 text-slate-500">#{{ $payment->id }}</td>
                        <td class="px-6 py-4 text-white">{{ $payment->user?->name }}</td>
                        <td class="px-6 py-4"><a href="{{ route('admin.orders.show', $payment->order) }}" class="text-indigo-600">{{ $payment->order?->order_number }}</a></td>
                        <td class="px-6 py-4 text-white">₹{{ number_format($payment->amount, 2) }}</td>
                        <td class="px-6 py-4 text-slate-500 capitalize">{{ $payment->gateway }}</td>
                        <td class="px-6 py-4"><x-badge :type="$payment->status === 'success' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger')">{{ ucfirst($payment->status) }}</x-badge></td>
                        <td class="px-6 py-4 text-slate-500">{{ $payment->paid_at?->format('M d, Y') ?? $payment->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4"><a href="{{ route('admin.payments.show', $payment) }}" class="text-indigo-600 text-sm">View</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $payments->links() }}</div>
        @else
        <x-empty-state title="No payments found" />
        @endif
    </div>
</div>
@endsection
