@extends('layouts.app')

@section('title', 'Edit '.$company->name)
@section('page-title', 'Edit institute')
@section('breadcrumb', 'Platform Admin / Institutes / Edit')

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="flex items-center justify-between gap-3">
        <a href="{{ route('platform.companies.show', $company) }}" class="text-sm text-slate-500 hover:text-slate-800">← Back to detail</a>
    </div>

    <form method="POST" action="{{ route('platform.companies.update', $company) }}" class="glass-card rounded-2xl p-6 space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <x-form-input label="Institute name" name="name" :value="old('name', $company->name)" required />
            </div>
            <x-form-input label="Slug" name="slug" :value="old('slug', $company->slug)" />
            <x-form-input label="Tagline" name="tagline" :value="old('tagline', $company->tagline)" />
            <x-form-input label="Email" name="email" type="email" :value="old('email', $company->email)" />
            <x-form-input label="Phone" name="phone" :value="old('phone', $company->phone)" />
            <x-form-input label="City" name="city" :value="old('city', $company->city)" />
            <x-form-input label="Website URL" name="website_url" :value="old('website_url', $company->website_url)" />
            <div class="sm:col-span-2">
                <x-form-input label="Address" name="address" :value="old('address', $company->address)" />
            </div>
            <div class="sm:col-span-2">
                <x-form-input label="About" name="about" type="textarea" :value="old('about', $company->about)" />
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Subscription package</label>
                <select name="subscription_package_id" class="panel-input w-full">
                    <option value="">No package</option>
                    @foreach($packages as $package)
                        <option value="{{ $package->id }}" @selected((string) old('subscription_package_id', $company->subscription_package_id) === (string) $package->id)>{{ $package->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex flex-wrap gap-6 pt-2">
            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                <input type="hidden" name="is_public" value="0">
                <input type="checkbox" name="is_public" value="1" @checked(old('is_public', $company->is_public)) class="rounded border-slate-300 text-teal-600">
                Public directory listing
            </label>
            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $company->is_active)) class="rounded border-slate-300 text-teal-600">
                Active (not suspended)
            </label>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('platform.companies.show', $company) }}" class="panel-btn-secondary">Cancel</a>
            <button type="submit" class="panel-btn-primary">Save changes</button>
        </div>
    </form>
</div>
@endsection
