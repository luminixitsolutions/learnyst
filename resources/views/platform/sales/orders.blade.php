@extends('layouts.app')

@section('title', 'All Orders')
@section('page-title', 'All Orders')
@section('breadcrumb', 'Platform Admin / Sales / Orders')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">Orders across all institutes (read-only oversight).</p>
        <a href="{{ route('platform.sales.orders.export', request()->query()) }}" class="panel-btn-primary text-sm">Export CSV</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-stat-card title="Orders" :value="number_format($stats['orders'])" />
        <x-stat-card title="Paid" :value="number_format($stats['paid'])" />
        <x-stat-card title="Revenue" :value="'₹'.number_format($stats['revenue'], 0)" />
        <x-stat-card title="Refunded / partial" :value="number_format($stats['refunded'])" />
    </div>

    <form method="GET" class="glass-card rounded-2xl p-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-3 items-end">
        <div class="xl:col-span-2">
            <label class="block text-xs font-medium text-slate-500 mb-1">Search</label>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Order #, customer…" class="panel-input w-full">
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
                @foreach(['pending','paid','failed','refunded','partial_refund'] as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ str_replace('_',' ', ucfirst($st)) }}</option>
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
            <a href="{{ route('platform.sales.orders') }}" class="panel-btn-secondary text-sm">Reset</a>
        </div>
    </form>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($orders->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Order</th>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Institute</th>
                        <th class="px-6 py-4">Total</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        @php
                            $badge = match ($order->payment_status) {
                                'paid' => 'success',
                                'failed', 'refunded' => 'danger',
                                'partial_refund' => 'warning',
                                default => 'info',
                            };
                        @endphp
                        <tr>
                            <td class="px-6 py-4 font-medium text-slate-800">{{ $order->order_number }}</td>
                            <td class="px-6 py-4">
                                <div>{{ $order->user?->name ?? '—' }}</div>
                                <div class="text-xs text-slate-400">{{ $order->user?->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($order->institute)
                                    <a href="{{ route('platform.companies.show', $order->institute) }}" class="text-indigo-600 hover:underline">{{ $order->institute->name }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-4 font-semibold">₹{{ number_format((float) $order->total, 0) }}</td>
                            <td class="px-6 py-4"><x-badge :type="$badge">{{ $order->payment_status }}</x-badge></td>
                            <td class="px-6 py-4 text-slate-500 whitespace-nowrap">{{ $order->created_at?->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('platform.sales.orders.show', $order) }}" class="text-xs font-semibold text-indigo-600">Detail</a>
                                    @if($order->institute?->is_active && $order->institute->owner)
                                        <form method="POST" action="{{ route('platform.companies.enter-panel', $order->institute) }}">@csrf
                                            <button class="text-xs font-semibold text-teal-700">Open panel</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t">{{ $orders->links() }}</div>
        @else
            <x-empty-state title="No orders found" />
        @endif
    </div>
</div>
@endsection
