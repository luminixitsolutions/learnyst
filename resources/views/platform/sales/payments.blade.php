@extends('layouts.app')

@section('title', 'Payments')
@section('page-title', 'Payments & refunds')
@section('breadcrumb', 'Platform Admin / Sales / Payments')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">Gateway payments and refunds across institutes.</p>
        <a href="{{ route('platform.sales.payments.export', request()->query()) }}" class="panel-btn-primary text-sm">Export CSV</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-stat-card title="Payments" :value="number_format($stats['total'])" />
        <x-stat-card title="Success amount" :value="'₹'.number_format($stats['success_amount'], 0)" />
        <x-stat-card title="Failed" :value="number_format($stats['failed'])" />
        <x-stat-card title="Refunded" :value="number_format($stats['refunded'])" />
    </div>

    @if($gstTotals)
        <div class="glass-card rounded-2xl p-4 flex flex-wrap gap-6 text-sm">
            <div><span class="text-slate-500">GST invoices</span> <span class="font-semibold text-slate-800 ml-2">{{ number_format($gstTotals['count']) }}</span></div>
            <div><span class="text-slate-500">Invoice total</span> <span class="font-semibold text-slate-800 ml-2">₹{{ number_format($gstTotals['total'], 0) }}</span></div>
            <div><span class="text-slate-500">Tax total</span> <span class="font-semibold text-slate-800 ml-2">₹{{ number_format($gstTotals['tax'], 0) }}</span></div>
        </div>
    @endif

    <form method="GET" class="glass-card rounded-2xl p-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-3 items-end">
        <div class="xl:col-span-2">
            <label class="block text-xs font-medium text-slate-500 mb-1">Search</label>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Txn / order / email…" class="panel-input w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Institute</label>
            <select name="company_id" class="panel-input w-full">
                <option value="">All</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}" @selected((string) request('company_id') === (string) $company->id)>{{ $company->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
            <select name="status" class="panel-input w-full">
                <option value="">All</option>
                @foreach(['pending','success','failed','refunded'] as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">From</label>
            <input type="date" name="from" value="{{ request('from') }}" class="panel-input w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">To</label>
            <input type="date" name="to" value="{{ request('to') }}" class="panel-input w-full">
        </div>
        <div class="flex gap-2 xl:col-span-6">
            <button class="panel-btn-primary text-sm">Filter</button>
            <a href="{{ route('platform.sales.payments') }}" class="panel-btn-secondary text-sm">Reset</a>
        </div>
    </form>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($payments->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Payment</th>
                        <th class="px-6 py-4">Order</th>
                        <th class="px-6 py-4">Institute</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4 text-right">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                        @php
                            $badge = match ($payment->status) {
                                'success' => 'success',
                                'failed', 'refunded' => 'danger',
                                default => 'warning',
                            };
                        @endphp
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium">{{ $payment->transaction_id ?: '#'.$payment->id }}</div>
                                <div class="text-xs text-slate-400">{{ $payment->gateway }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($payment->order)
                                    <a href="{{ route('platform.sales.orders.show', $payment->order) }}" class="text-indigo-600 hover:underline">{{ $payment->order->order_number }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ $payment->institute?->name ?? '—' }}</td>
                            <td class="px-6 py-4 font-semibold">₹{{ number_format((float) $payment->amount, 0) }}</td>
                            <td class="px-6 py-4"><x-badge :type="$badge">{{ $payment->status }}</x-badge></td>
                            <td class="px-6 py-4 text-slate-500">{{ $payment->created_at?->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('platform.sales.payments.show', $payment) }}" class="text-xs font-semibold text-indigo-600">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t">{{ $payments->links() }}</div>
        @else
            <x-empty-state title="No payments found" />
        @endif
    </div>
</div>
@endsection
