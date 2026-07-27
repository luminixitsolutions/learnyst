@extends('layouts.app')

@section('title', 'Coupon Report')
@section('page-title', 'Coupon Report')
@section('breadcrumb', 'Reports / Sales / Coupons')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search coupon code...">
        <x-slot:filters>
            <select name="status" class="px-3 py-2 rounded-xl border border-slate-200 text-sm">
                <option value="">All Status</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
            </select>
        </x-slot:filters>
    </x-report-toolbar>

    <x-admin.report-datatable table-id="couponsReportTable" :has-records="$coupons->count() > 0" entity="coupons" :order-column="3" order-direction="desc" export-file-name="coupons-report" empty-title="No coupons found">
        <thead><tr class="text-left">
            <th>Coupon Code</th><th>Discount Type</th><th>Discount Value</th><th>Usage Count</th><th>Total Discount Given</th><th>Start Date</th><th>End Date</th><th>Status</th>
        </tr></thead>
        <tbody>
            @foreach($coupons as $coupon)
            <tr>
                <td class="font-mono text-indigo-600">{{ $coupon->code }}</td>
                <td class="capitalize">{{ $coupon->discount_type }}</td>
                <td>{{ $coupon->discount_type === 'percentage' ? $coupon->discount_value.'%' : '₹'.$coupon->discount_value }}</td>
                <td data-order="{{ $coupon->used_count }}">{{ $coupon->used_count }}</td>
                <td data-order="{{ \App\Models\Order::where('coupon_id', $coupon->id)->sum('discount') }}">₹{{ number_format(\App\Models\Order::where('coupon_id', $coupon->id)->sum('discount'), 2) }}</td>
                <td class="text-slate-500" data-order="{{ $coupon->starts_at?->timestamp ?? 0 }}">{{ $coupon->starts_at?->format('M d, Y') ?? '—' }}</td>
                <td class="text-slate-500" data-order="{{ $coupon->expires_at?->timestamp ?? 0 }}">{{ $coupon->expires_at?->format('M d, Y') ?? '—' }}</td>
                <td><x-badge :type="$coupon->is_active ? 'success' : 'default'">{{ $coupon->is_active ? 'Active' : 'Inactive' }}</x-badge></td>
            </tr>
            @endforeach
        </tbody>
    </x-admin.report-datatable>
</div>
@endsection
