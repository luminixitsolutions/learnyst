@extends('layouts.app')

@section('title', 'Create institute')
@section('page-title', 'Create institute')
@section('breadcrumb', 'Platform Admin / Institutes / Create')

@section('content')
<div class="max-w-3xl space-y-6">
    <a href="{{ route('platform.companies.index') }}" class="text-sm text-slate-500 hover:text-slate-800">← All institutes</a>

    <form method="POST" action="{{ route('platform.companies.store') }}" class="space-y-6">
        @csrf

        <div class="glass-card rounded-2xl p-6 space-y-5">
            <h3 class="text-sm font-semibold text-slate-900 uppercase tracking-wide">Institute</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <x-form-input label="Institute name" name="institute_name" :value="old('institute_name')" required />
                </div>
                <x-form-input label="Tagline" name="tagline" :value="old('tagline')" />
                <x-form-input label="City" name="city" :value="old('city')" />
                <x-form-input label="Email" name="email" type="email" :value="old('email')" />
                <x-form-input label="Phone" name="phone" :value="old('phone')" />
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Subscription package</label>
                    <select name="subscription_package_id" class="panel-input w-full">
                        <option value="">No package</option>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}" @selected((string) old('subscription_package_id') === (string) $package->id)>{{ $package->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                <input type="hidden" name="is_public" value="0">
                <input type="checkbox" name="is_public" value="1" @checked(old('is_public', true)) class="rounded border-slate-300 text-teal-600">
                List publicly
            </label>
        </div>

        <div class="glass-card rounded-2xl p-6 space-y-5">
            <h3 class="text-sm font-semibold text-slate-900 uppercase tracking-wide">Owner admin account</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input label="Owner name" name="owner_name" :value="old('owner_name')" required />
                <x-form-input label="Owner email" name="owner_email" type="email" :value="old('owner_email')" required />
                <x-form-input label="Password" name="owner_password" type="password" required />
                <x-form-input label="Confirm password" name="owner_password_confirmation" type="password" required />
            </div>
            <p class="text-xs text-slate-500">Creates an institute admin user who can sign in at /login and manage this tenant.</p>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('platform.companies.index') }}" class="panel-btn-secondary">Cancel</a>
            <button type="submit" class="panel-btn-primary">Create institute</button>
        </div>
    </form>
</div>
@endsection
