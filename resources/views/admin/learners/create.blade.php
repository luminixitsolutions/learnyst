@extends('layouts.app')

@section('title', 'Add Learner')
@section('page-title', 'Add Learner')
@section('breadcrumb', 'Learners / Create')

@section('content')
<div class="max-w-2xl">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.learners.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <x-form-input label="Full Name" name="name" required />
            <x-form-input label="Email" name="email" type="email" required />
            <x-form-input label="Mobile Number" name="phone" />
            <x-form-input label="Password" name="password" type="password" required />
            <x-form-input label="Address" name="address" type="textarea" />
            <x-form-input label="Notes" name="notes" type="textarea" placeholder="Internal notes about this learner" />
            <x-form-input label="Referral Code (optional)" name="referral_code" :value="old('referral_code')" placeholder="Apply a referrer's code on signup" />
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700">Profile Photo</label>
                <input type="file" name="avatar" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-slate-900 file:text-white">
            </div>
            <div class="flex flex-wrap gap-6">
                <label class="flex items-center gap-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="rounded border-slate-300 text-indigo-600">
                    <span class="text-sm text-slate-700">Status: Active</span>
                </label>
                <label class="flex items-center gap-3">
                    <input type="hidden" name="email_verified" value="0">
                    <input type="checkbox" name="email_verified" value="1" @checked(old('email_verified', true)) class="rounded border-slate-300 text-indigo-600">
                    <span class="text-sm text-slate-700">Email Verified</span>
                </label>
            </div>
            <div class="flex justify-between pt-4">
                <a href="{{ route('admin.learners.index') }}" class="px-4 py-2 text-sm text-slate-500 hover:text-slate-800">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Create Learner</button>
            </div>
        </form>
    </div>
</div>
@endsection
