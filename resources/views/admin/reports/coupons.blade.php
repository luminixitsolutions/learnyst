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

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($coupons->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                    <th class="px-6 py-4">Coupon Code</th>
                    <th class="px-6 py-4">Discount Type</th>
                    <th class="px-6 py-4">Discount Value</th>
                    <th class="px-6 py-4">Usage Count</th>
                    <th class="px-6 py-4">Total Discount Given</th>
                    <th class="px-6 py-4">Start Date</th>
                    <th class="px-6 py-4">End Date</th>
                    <th class="px-6 py-4">Status</th>
                </tr></thead>
                <tbody>
                    @foreach($coupons as $coupon)
                    <tr>
                        <td class="px-6 py-4 font-mono text-indigo-600">{{ $coupon->code }}</td>
                        <td class="px-6 py-4 capitalize">{{ $coupon->discount_type }}</td>
                        <td class="px-6 py-4">{{ $coupon->discount_type === 'percent' ? $coupon->discount_value.'%' : '₹'.$coupon->discount_value }}</td>
                        <td class="px-6 py-4">{{ $coupon->used_count }}</td>
                        <td class="px-6 py-4">₹{{ number_format(\App\Models\Order::where('coupon_id', $coupon->id)->sum('discount'), 2) }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $coupon->starts_at?->format('M d, Y') ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $coupon->expires_at?->format('M d, Y') ?? '—' }}</td>
                        <td class="px-6 py-4"><x-badge :type="$coupon->is_active ? 'success' : 'default'">{{ $coupon->is_active ? 'Active' : 'Inactive' }}</x-badge></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $coupons->links() }}</div>
        @else
        <x-empty-state title="No coupons found" />
        @endif
    </div>
</div>
@endsection
