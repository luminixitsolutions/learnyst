@extends('layouts.app')

@section('title', 'Wallet Settings')
@section('page-title', 'Wallet Settings')
@section('breadcrumb', 'Sales / Wallets / Settings')

@section('content')
<div class="max-w-2xl space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.wallets.settings.update') }}" class="space-y-5">
            @csrf
            @method('PUT')

            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="enabled" value="1" @checked(old('enabled', ($settings['enabled'] ?? '1') === '1')) class="rounded border-slate-300 text-emerald-600">
                <span class="text-sm text-slate-800">Enable learner wallets</span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="allow_checkout_redeem" value="1" @checked(old('allow_checkout_redeem', ($settings['allow_checkout_redeem'] ?? '1') === '1')) class="rounded border-slate-300 text-emerald-600">
                <span class="text-sm text-slate-800">Allow wallet spend at checkout</span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="refund_to_wallet" value="1" @checked(old('refund_to_wallet', ($settings['refund_to_wallet'] ?? '1') === '1')) class="rounded border-slate-300 text-emerald-600">
                <span class="text-sm text-slate-800">Credit refunds to wallet</span>
            </label>

            <x-form-input label="Signup Bonus (₹)" name="signup_bonus" type="number" step="0.01" :value="old('signup_bonus', $settings['signup_bonus'] ?? 0)" />
            <x-form-input label="Minimum Top-up (₹)" name="min_topup" type="number" step="0.01" :value="old('min_topup', $settings['min_topup'] ?? 0)" />

            <div class="flex justify-between pt-2">
                <a href="{{ route('admin.wallets.index') }}" class="text-sm text-slate-500 hover:text-slate-800">Back</a>
                <button type="submit" class="panel-btn-primary">Save Settings</button>
            </div>
        </form>
    </div>
</div>
@endsection
