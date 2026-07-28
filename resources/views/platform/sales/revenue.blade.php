@extends('layouts.app')

@section('title', 'Revenue by Institute')
@section('page-title', 'Revenue by Institute')
@section('breadcrumb', 'Platform Admin / Sales / Revenue')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">Paid order revenue attributed via course ownership / learner institute.</p>
        <a href="{{ route('platform.sales.revenue.export', request()->query()) }}" class="panel-btn-primary text-sm">Export CSV</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-stat-card title="Institutes" :value="number_format($totals['institutes'])" />
        <x-stat-card title="Paid orders" :value="number_format($totals['orders'])" />
        <x-stat-card title="Total revenue" :value="'₹'.number_format($totals['revenue'], 0)" />
    </div>

    <form method="GET" class="glass-card rounded-2xl p-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">From</label>
            <input type="date" name="from" value="{{ request('from') }}" class="panel-input">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">To</label>
            <input type="date" name="to" value="{{ request('to') }}" class="panel-input">
        </div>
        <button class="panel-btn-primary text-sm">Apply</button>
        <a href="{{ route('platform.sales.revenue') }}" class="panel-btn-secondary text-sm">Reset</a>
    </form>

    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Institute</th>
                        <th class="px-6 py-4">Package</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Paid orders</th>
                        <th class="px-6 py-4">Revenue</th>
                        <th class="px-6 py-4">Avg order</th>
                        <th class="px-6 py-4 text-right">Links</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                        <tr>
                            <td class="px-6 py-4 font-medium">
                                <a href="{{ route('platform.companies.show', $row['company']) }}" class="text-indigo-600 hover:underline">{{ $row['company']->name }}</a>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $row['company']->subscriptionPackage?->name ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <x-badge :type="$row['company']->is_active ? 'success' : 'danger'">{{ $row['company']->is_active ? 'Active' : 'Suspended' }}</x-badge>
                            </td>
                            <td class="px-6 py-4">{{ number_format($row['orders']) }}</td>
                            <td class="px-6 py-4 font-semibold">₹{{ number_format($row['revenue'], 0) }}</td>
                            <td class="px-6 py-4">₹{{ number_format($row['avg_order'], 0) }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('platform.sales.orders', ['company_id' => $row['company']->id, 'status' => 'paid']) }}" class="text-xs font-semibold text-indigo-600">Orders</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
