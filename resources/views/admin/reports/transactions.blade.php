@extends('layouts.app')

@section('title', 'Transactions Report')
@section('page-title', 'Transactions Report')
@section('breadcrumb', 'Reports / Transactions')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search name, email, order or payment id..." :showDateRange="true" :from="request('from', $from)" :to="request('to', $to)">
        <x-slot:filters>
            <select name="status" class="px-3 py-2 rounded-xl border border-slate-200 text-sm">
                <option value="">All Status</option>
                @foreach(['success','failed','pending'] as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
            <select name="gateway" class="px-3 py-2 rounded-xl border border-slate-200 text-sm">
                <option value="">All Gateways</option>
                @foreach(['razorpay','manual','free'] as $gw)
                    <option value="{{ $gw }}" @selected(request('gateway') === $gw)>{{ ucfirst($gw) }}</option>
                @endforeach
            </select>
        </x-slot:filters>
    </x-report-toolbar>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($payments->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                    <th class="px-6 py-4">Order ID</th>
                    <th class="px-6 py-4">Learner</th>
                    <th class="px-6 py-4">Product</th>
                    <th class="px-6 py-4">Amount</th>
                    <th class="px-6 py-4">Payment Mode</th>
                    <th class="px-6 py-4">Payment Status</th>
                    <th class="px-6 py-4">Transaction ID</th>
                    <th class="px-6 py-4">Created Date</th>
                </tr></thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4"><a href="{{ route('admin.orders.show', $payment->order) }}" class="text-indigo-600">{{ $payment->order?->order_number }}</a></td>
                        <td class="px-6 py-4 text-slate-800">{{ $payment->user?->name }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $payment->order?->items->first()?->course?->title ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-800">₹{{ number_format($payment->amount, 2) }}</td>
                        <td class="px-6 py-4 capitalize text-slate-500">{{ $payment->gateway }}</td>
                        <td class="px-6 py-4"><x-badge :type="$payment->status === 'success' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger')">{{ ucfirst($payment->status) }}</x-badge></td>
                        <td class="px-6 py-4 text-slate-500 font-mono text-xs">{{ $payment->transaction_id ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $payment->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $payments->links() }}</div>
        @else
        <x-empty-state title="No transactions found" />
        @endif
    </div>
</div>
@endsection
