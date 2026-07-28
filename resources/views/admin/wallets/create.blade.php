@extends('layouts.app')

@section('title', 'Create Wallet')
@section('page-title', 'Create Wallet')
@section('breadcrumb', 'Sales / Wallets / Create')

@section('content')
<div class="max-w-2xl">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.wallets.store') }}" class="space-y-5">
            @csrf
            <x-form-input label="Learner" name="user_id" type="select" required>
                <option value="">Select learner</option>
                @foreach($learners as $learner)
                    <option value="{{ $learner->id }}" @selected(old('user_id') == $learner->id)>{{ $learner->name }} ({{ $learner->email }})</option>
                @endforeach
            </x-form-input>
            <x-form-input label="Opening Balance (₹)" name="opening_balance" type="number" step="0.01" :value="old('opening_balance', 0)" />
            <x-form-input label="Notes" name="notes" type="textarea" :value="old('notes')" />
            <div class="flex justify-between pt-2">
                <a href="{{ route('admin.wallets.index') }}" class="text-sm text-slate-500 hover:text-slate-800">Cancel</a>
                <button type="submit" class="panel-btn-primary">Create Wallet</button>
            </div>
        </form>
    </div>
</div>
@endsection
