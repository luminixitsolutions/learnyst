@extends('layouts.app')

@section('title', 'Payment Gateways Report')
@section('page-title', 'Payment Gateways Report')
@section('breadcrumb', 'Reports / Payment Gateways')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search by payment gateway...">
        <x-slot:filters>
            <select name="status" class="px-3 py-2 rounded-xl border border-slate-200 text-sm">
                <option value="">All Status</option>
                <option value="success" @selected(request('status') === 'success')>Success</option>
                <option value="failed" @selected(request('status') === 'failed')>Failed</option>
            </select>
        </x-slot:filters>
    </x-report-toolbar>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($gateways->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                    <th class="px-6 py-4">Gateway Name</th>
                    <th class="px-6 py-4">Updated By</th>
                    <th class="px-6 py-4">Configuration Type</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Transactions</th>
                    <th class="px-6 py-4">Total Amount</th>
                </tr></thead>
                <tbody>
                    @foreach($gateways as $gateway)
                    <tr>
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $gateway->name }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $gateway->updated_by }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $gateway->config_type }}</td>
                        <td class="px-6 py-4"><x-badge type="success">{{ $gateway->status }}</x-badge></td>
                        <td class="px-6 py-4 text-slate-800">{{ number_format($gateway->transaction_count) }}</td>
                        <td class="px-6 py-4 text-indigo-600">₹{{ number_format($gateway->total_amount ?? 0, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No payment gateway data" />
        @endif
    </div>
</div>
@endsection
