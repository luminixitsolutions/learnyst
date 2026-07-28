@extends('layouts.app')

@section('title', 'Create Order')
@section('page-title', 'Create Order')
@section('breadcrumb', 'Orders / Manual Order')

@section('content')
<div class="max-w-3xl">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.orders.store') }}" class="space-y-6">
            @csrf
            <x-form-input label="Learner" name="user_id" type="select" required>
                <option value="">Select learner</option>
                @foreach($learners as $learner)
                    <option value="{{ $learner->id }}" @selected(old('user_id') == $learner->id)>{{ $learner->name }} ({{ $learner->email }})</option>
                @endforeach
            </x-form-input>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-slate-300">Courses <span class="text-red-400">*</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto p-4 rounded-xl bg-slate-900/80 border border-slate-200">
                    @foreach($courses as $course)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="course_ids[]" value="{{ $course->id }}" @checked(in_array($course->id, old('course_ids', []))) class="rounded border-slate-600 bg-slate-800 text-brand-500">
                        <span class="text-sm text-slate-300">{{ $course->title }} — ₹{{ number_format($course->price, 0) }}</span>
                    </label>
                    @endforeach
                </div>
                @error('course_ids')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-form-input label="Coupon" name="coupon_id" type="select">
                    <option value="">None</option>
                    @foreach($coupons as $coupon)
                        <option value="{{ $coupon->id }}" @selected(old('coupon_id') == $coupon->id)>{{ $coupon->code }} ({{ $coupon->discount_type === 'percentage' ? $coupon->discount_value.'%' : '₹'.$coupon->discount_value }})</option>
                    @endforeach
                </x-form-input>
                <x-form-input label="Extra Discount (₹)" name="discount" type="number" step="0.01" :value="old('discount', 0)" />
                <x-form-input label="Payment Method" name="payment_method" type="select" required>
                    @foreach(['razorpay','manual','free','wallet'] as $m)
                        <option value="{{ $m }}" @selected(old('payment_method') === $m)>{{ ucfirst($m) }}</option>
                    @endforeach
                </x-form-input>
                <x-form-input label="Payment Status" name="payment_status" type="select" required>
                    @foreach(['pending','paid','failed'] as $st)
                        <option value="{{ $st }}" @selected(old('payment_status', 'paid') === $st)>{{ ucfirst($st) }}</option>
                    @endforeach
                </x-form-input>
                <x-form-input label="Wallet Amount (₹)" name="wallet_amount" type="number" step="0.01" :value="old('wallet_amount', 0)" />
            </div>
            <p class="text-xs text-slate-500 -mt-3">For full wallet checkout, set payment method to Wallet. Or enter a partial wallet amount with another method.</p>
            <x-form-input label="Affiliate Code (optional)" name="affiliate_code" :value="old('affiliate_code')" placeholder="Track affiliate conversion" />
            <x-form-input label="Notes" name="notes" type="textarea" />

            @if(isset($consents) && $consents->count())
            <div class="space-y-3 pt-2 border-t border-slate-200">
                <label class="block text-sm font-medium text-slate-300">Checkout Consents</label>
                <div class="space-y-3 p-4 rounded-xl bg-slate-900/80 border border-slate-200">
                    @foreach($consents as $consent)
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="hidden" name="consents[{{ $consent->id }}]" value="0">
                        <input type="checkbox" name="consents[{{ $consent->id }}]" value="1"
                               @checked(old("consents.{$consent->id}", false))
                               class="mt-1 rounded border-slate-600 bg-slate-800 text-brand-500">
                        <div>
                            <span class="text-sm text-white">{{ $consent->title }}</span>
                            @if($consent->is_required)<x-badge type="warning" class="ml-2">Required</x-badge>@endif
                            @if($consent->description)<p class="text-xs text-slate-500 mt-1">{{ $consent->description }}</p>@endif
                            <p class="text-xs text-slate-500 mt-1">{{ Str::limit(strip_tags($consent->body), 120) }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('consents')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            @endif

            <div class="flex justify-between pt-4">
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-slate-500 hover:text-white">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Create Order</button>
            </div>
        </form>
    </div>
</div>
@endsection
