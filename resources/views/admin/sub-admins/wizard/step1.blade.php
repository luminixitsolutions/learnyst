@extends('layouts.app')

@section('title', 'Sub Admin Wizard — Details')
@section('page-title', 'Create Sub Admin')
@section('breadcrumb', 'Wizard / Step 1 — Details')

@section('content')
<div class="max-w-2xl mx-auto">
    @include('admin.sub-admins.wizard._progress', ['current' => 1])

    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.sub-admins.wizard.store', 1) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <x-form-input label="Full Name" name="name" :value="old('name', $data['details']['name'] ?? '')" required />
            <x-form-input label="Email" name="email" type="email" :value="old('email', $data['details']['email'] ?? '')" required />
            <x-form-input label="Phone" name="phone" :value="old('phone', $data['details']['phone'] ?? '')" />
            <x-form-input label="Password" name="password" type="password" required />
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-300">Avatar</label>
                <input type="file" name="avatar" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-slate-800 file:text-slate-300">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach(['facebook', 'linkedin', 'twitter', 'website'] as $platform)
                <x-form-input :label="ucfirst($platform)" :name="'social_links['.$platform.']'" :value="old('social_links.'.$platform, $data['details']['social_links'][$platform] ?? '')" placeholder="https://" />
                @endforeach
            </div>
            <div class="flex justify-between pt-4 border-t border-slate-200">
                <a href="{{ route('admin.sub-admins.index') }}" class="text-sm text-slate-500 hover:text-white">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Next: Role →</button>
            </div>
        </form>
    </div>
</div>
@endsection
