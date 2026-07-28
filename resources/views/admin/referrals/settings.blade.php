@extends('layouts.app')

@section('title', 'Referral Settings')
@section('page-title', 'Referral Settings')
@section('breadcrumb', 'Sales / Referrals / Settings')

@section('content')
<div class="max-w-2xl space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.referrals.settings.update') }}" class="space-y-5">
            @csrf
            @method('PUT')

            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="enabled" value="1" @checked(old('enabled', ($settings['enabled'] ?? '1') === '1')) class="rounded border-slate-300 text-emerald-600">
                <span class="text-sm text-slate-800">Enable referral program</span>
            </label>

            <x-form-input label="Reward Type" name="reward_type" type="select" required>
                @foreach(['wallet' => 'Wallet credit', 'coupon' => 'Coupon', 'free_days' => 'Free access days'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('reward_type', $settings['reward_type'] ?? 'wallet') === $val)>{{ $label }}</option>
                @endforeach
            </x-form-input>

            <x-form-input label="Reward Trigger" name="reward_on" type="select" required>
                @foreach(['signup' => 'On signup / code apply', 'first_purchase' => 'On first paid purchase'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('reward_on', $settings['reward_on'] ?? 'signup') === $val)>{{ $label }}</option>
                @endforeach
            </x-form-input>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input label="Referrer Reward (₹)" name="referrer_reward" type="number" step="0.01" :value="old('referrer_reward', $settings['referrer_reward'] ?? 100)" required />
                <x-form-input label="Referred Reward (₹)" name="referred_reward" type="number" step="0.01" :value="old('referred_reward', $settings['referred_reward'] ?? 50)" required />
            </div>

            <x-form-input label="Free Days (if reward type is free days)" name="free_days" type="number" :value="old('free_days', $settings['free_days'] ?? 0)" />

            <div class="flex justify-between pt-2">
                <a href="{{ route('admin.referrals.index') }}" class="text-sm text-slate-500 hover:text-slate-800">Back</a>
                <button type="submit" class="panel-btn-primary">Save Settings</button>
            </div>
        </form>
    </div>
</div>
@endsection
