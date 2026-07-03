@extends('layouts.app')

@section('title', 'Payments Report')
@section('page-title', 'Payments Report')
@section('breadcrumb', 'Reports / Payments')

@section('content')
<div class="space-y-6">
    <form method="GET" class="flex gap-3">
        <select name="status" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm">
            <option value="">All Status</option>
            @foreach(['success','failed','pending'] as $st)
                <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
            @endforeach
        </select>
        <button type="submit" class="panel-btn-secondary">Filter</button>
        <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 text-sm text-slate-500">← All Reports</a>
    </form>
    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Order</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Gateway</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr>
                        <td class="px-6 py-4 text-white">{{ $payment->user?->name }}</td>
                        <td class="px-6 py-4 text-indigo-600">{{ $payment->order?->order_number }}</td>
                        <td class="px-6 py-4">₹{{ number_format($payment->amount, 2) }}</td>
                        <td class="px-6 py-4 capitalize">{{ $payment->gateway }}</td>
                        <td class="px-6 py-4"><x-badge :type="$payment->status === 'success' ? 'success' : 'danger'">{{ ucfirst($payment->status) }}</x-badge></td>
                        <td class="px-6 py-4 text-slate-500">{{ $payment->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $payments->links() }}</div>
    </div>
</div>
@endsection
