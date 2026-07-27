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

    <x-admin.report-datatable table-id="productSalesReportTable" :has-records="$items->count() > 0" entity="product sales" :order-column="7" order-direction="desc" export-file-name="product-sales-report" empty-title="No product sales in this period">
        <thead><tr class="text-left">
            <th>Product</th><th>Order</th><th>Learner</th><th>Net Amount</th><th>Discount</th><th>Coupon</th><th>Payment Status</th><th>Date</th>
        </tr></thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td class="text-slate-800">{{ $item->course?->title ?? '—' }}</td>
                <td><a href="{{ route('admin.orders.show', $item->order) }}" class="text-indigo-600">{{ $item->order?->order_number }}</a></td>
                <td>{{ $item->order?->user?->name }}</td>
                <td data-order="{{ $item->total }}">₹{{ number_format($item->total, 2) }}</td>
                <td data-order="{{ $item->discount ?? 0 }}">₹{{ number_format($item->discount ?? 0, 2) }}</td>
                <td>{{ $item->order?->coupon?->code ?? '—' }}</td>
                <td><x-badge :type="$item->order?->payment_status === 'paid' ? 'success' : 'warning'">{{ ucfirst($item->order?->payment_status ?? '—') }}</x-badge></td>
                <td class="text-slate-500" data-order="{{ $item->order?->created_at?->timestamp ?? 0 }}">{{ $item->order?->created_at?->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </x-admin.report-datatable>
</div>
@endsection
