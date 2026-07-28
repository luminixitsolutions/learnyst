@extends('layouts.app')

@section('title', 'Coupons')
@section('page-title', 'Coupons')
@section('breadcrumb', 'Marketing / Coupons')

@push('styles')
    <x-admin.datatable-styles />
@endpush

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
            <x-form-input label="Global Usage Limit" name="max_uses" type="number" />
            <x-form-input label="Per-User Limit" name="per_user_limit" type="number" />
            <x-form-input label="Starts At" name="starts_at" type="date" />
            <x-form-input label="Expires At" name="expires_at" type="date" />
            <div class="md:col-span-3">
                <label class="block text-sm text-slate-600 mb-2">Restrict to courses (leave empty = all products)</label>
                <select name="course_ids[]" multiple class="w-full rounded-xl bg-white border border-slate-200 text-slate-800 min-h-[100px]">
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>
            <x-form-input label="Description" name="description" type="textarea" class="md:col-span-3" />
            <label class="flex items-center gap-3 md:col-span-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 bg-white text-brand-500">
                <span class="text-sm text-slate-600">Active</span>
            </label>
            <div class="md:col-span-3">
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Create Coupon</button>
            </div>
        </form>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($coupons->count())
        <div class="overflow-x-auto">
            <table id="couponsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Code</th>
                        <th class="px-6 py-4">Discount</th>
                        <th class="px-6 py-4">Uses</th>
                        <th class="px-6 py-4">Per user</th>
                        <th class="px-6 py-4">Courses</th>
                        <th class="px-6 py-4">Expires</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($coupons as $coupon)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 text-slate-800 font-mono">{{ $coupon->code }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $coupon->discount_type === 'percentage' ? $coupon->discount_value.'%' : '₹'.$coupon->discount_value }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $coupon->used_count ?? 0 }}/{{ $coupon->max_uses ?? '∞' }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $coupon->per_user_limit ?? '∞' }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $coupon->courses->count() ? $coupon->courses->pluck('title')->take(2)->join(', ') : 'All' }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $coupon->expires_at?->format('M d, Y') ?? '—' }}</td>
                        <td class="px-6 py-4"><x-badge :type="$coupon->is_active ? 'success' : 'danger'">{{ $coupon->is_active ? 'Active' : 'Inactive' }}</x-badge></td>
                        <td class="px-6 py-4">
                            <form method="POST" action="{{ route('admin.marketing.coupons.destroy', $coupon) }}">@csrf @method('DELETE')
                                <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-red-500 text-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No coupons yet" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($coupons->count())
    <x-admin.datatable-scripts table-id="couponsTable" entity="coupons" :order-column="0" order-direction="desc" :action-column="7" export-file-name="coupons" />
@endif
@endpush
