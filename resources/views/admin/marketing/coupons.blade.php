@extends('layouts.app')

@section('title', 'Coupons')
@section('page-title', 'Coupons')
@section('breadcrumb', 'Marketing / Coupons')

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Create Coupon</h3>
        <form method="POST" action="{{ route('admin.marketing.coupons.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <x-form-input label="Code" name="code" required placeholder="SAVE20" />
            <x-form-input label="Title" name="title" />
            <x-form-input label="Discount Type" name="discount_type" type="select" required>
                <option value="fixed">Fixed (₹)</option>
                <option value="percentage">Percentage (%)</option>
            </x-form-input>
            <x-form-input label="Discount Value" name="discount_value" type="number" step="0.01" required />
            <x-form-input label="Minimum Order Amount (₹)" name="min_order_amount" type="number" step="0.01" :value="old('min_order_amount')" />
            <x-form-input label="Usage Limit" name="max_uses" type="number" />
            <x-form-input label="Starts At" name="starts_at" type="date" />
            <x-form-input label="Expires At" name="expires_at" type="date" />
            <label class="flex items-center gap-3 md:col-span-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-600 bg-slate-800 text-brand-500">
                <span class="text-sm text-slate-300">Active</span>
            </label>
            <div class="md:col-span-3">
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Create Coupon</button>
            </div>
        </form>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($coupons->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                        <th class="px-6 py-4">Code</th>
                        <th class="px-6 py-4">Discount</th>
                        <th class="px-6 py-4">Uses</th>
                        <th class="px-6 py-4">Expires</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($coupons as $coupon)
                    <tr>
                        <td class="px-6 py-4 text-white font-mono">{{ $coupon->code }}</td>
                        <td class="px-6 py-4">{{ $coupon->discount_type === 'percentage' ? $coupon->discount_value.'%' : '₹'.$coupon->discount_value }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $coupon->used_count ?? 0 }}/{{ $coupon->max_uses ?? '∞' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $coupon->expires_at?->format('M d, Y') ?? '—' }}</td>
                        <td class="px-6 py-4"><x-badge :type="$coupon->is_active ? 'success' : 'danger'">{{ $coupon->is_active ? 'Active' : 'Inactive' }}</x-badge></td>
                        <td class="px-6 py-4">
                            <form method="POST" action="{{ route('admin.marketing.coupons.destroy', $coupon) }}">@csrf @method('DELETE')
                                <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-red-400 text-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $coupons->links() }}</div>
        @else
        <x-empty-state title="No coupons yet" />
        @endif
    </div>
</div>
@endsection
