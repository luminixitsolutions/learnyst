@extends('layouts.app')

@section('title', 'Product Sales Report')
@section('page-title', 'Product Sales Report')
@section('breadcrumb', 'Reports / Sales / Product Sales')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search product, order or learner..." :showDateRange="true" :from="$from" :to="$to">
        <x-slot:filters>
            <select name="status" class="px-3 py-2 rounded-xl border border-slate-200 text-sm">
                <option value="">All Payment Status</option>
                @foreach(['paid','pending','failed'] as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
        </x-slot:filters>
    </x-report-toolbar>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($items->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                    <th class="px-6 py-4">Product</th>
                    <th class="px-6 py-4">Order</th>
                    <th class="px-6 py-4">Learner</th>
                    <th class="px-6 py-4">Net Amount</th>
                    <th class="px-6 py-4">Discount</th>
                    <th class="px-6 py-4">Coupon</th>
                    <th class="px-6 py-4">Payment Status</th>
                    <th class="px-6 py-4">Date</th>
                </tr></thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td class="px-6 py-4 text-slate-800">{{ $item->course?->title ?? '—' }}</td>
                        <td class="px-6 py-4"><a href="{{ route('admin.orders.show', $item->order) }}" class="text-indigo-600">{{ $item->order?->order_number }}</a></td>
                        <td class="px-6 py-4">{{ $item->order?->user?->name }}</td>
                        <td class="px-6 py-4">₹{{ number_format($item->total, 2) }}</td>
                        <td class="px-6 py-4">₹{{ number_format($item->discount ?? 0, 2) }}</td>
                        <td class="px-6 py-4">{{ $item->order?->coupon?->code ?? '—' }}</td>
                        <td class="px-6 py-4"><x-badge :type="$item->order?->payment_status === 'paid' ? 'success' : 'warning'">{{ ucfirst($item->order?->payment_status ?? '—') }}</x-badge></td>
                        <td class="px-6 py-4 text-slate-500">{{ $item->order?->created_at?->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $items->links() }}</div>
        @else
        <x-empty-state title="No product sales in this period" />
        @endif
    </div>
</div>
@endsection
