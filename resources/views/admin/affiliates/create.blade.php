@extends('layouts.app')

@section('title', 'Add Affiliate')
@section('page-title', 'Register Affiliate')
@section('breadcrumb', 'Sales / Affiliates / Create')

@section('content')
<div class="max-w-2xl">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.affiliates.store') }}" class="space-y-5">
            @csrf
            <x-form-input label="Link Learner (optional)" name="user_id" type="select">
                <option value="">No linked learner</option>
                @foreach($learners as $learner)
                    <option value="{{ $learner->id }}" @selected(old('user_id') == $learner->id)>{{ $learner->name }} ({{ $learner->email }})</option>
                @endforeach
            </x-form-input>
            <x-form-input label="Name" name="name" :value="old('name')" required />
            <x-form-input label="Email" name="email" type="email" :value="old('email')" required />
            <x-form-input label="Phone" name="phone" :value="old('phone')" />
            <x-form-input label="Affiliate Code (optional)" name="code" :value="old('code')" />
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input label="Commission Type" name="commission_type" type="select" required>
                    <option value="percent" @selected(old('commission_type', 'percent') === 'percent')>Percent</option>
                    <option value="fixed" @selected(old('commission_type') === 'fixed')>Fixed (₹)</option>
                </x-form-input>
                <x-form-input label="Commission Value" name="commission_value" type="number" step="0.01" :value="old('commission_value', 10)" required />
            </div>
            <x-form-input label="Payment Details" name="payment_details" type="textarea" :value="old('payment_details')" />
            <x-form-input label="Notes" name="notes" type="textarea" :value="old('notes')" />
            <div class="flex justify-between pt-2">
                <a href="{{ route('admin.affiliates.index') }}" class="text-sm text-slate-500 hover:text-slate-800">Cancel</a>
                <button type="submit" class="panel-btn-primary">Register Affiliate</button>
            </div>
        </form>
    </div>
</div>
@endsection
