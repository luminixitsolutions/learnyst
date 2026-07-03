@extends('layouts.app')

@section('title', 'Profile')
@section('page-title', 'My Profile')
@section('breadcrumb', 'Account Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-semibold text-white mb-6">Profile Information</h3>
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')
            <div class="flex items-center gap-6 mb-6">
                <div class="w-20 h-20 rounded-2xl bg-slate-800 flex items-center justify-center text-2xl font-bold text-indigo-600 overflow-hidden">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" alt="" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Avatar</label>
                    <input type="file" name="avatar" accept="image/*" class="text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-brand-600 file:text-white file:cursor-pointer">
                    @error('avatar')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-form-input label="Full Name" name="name" :value="$user->name" required />
                <x-form-input label="Email" name="email" type="email" :value="$user->email" required />
                <x-form-input label="Phone" name="phone" :value="$user->phone" />
            </div>
            <x-form-input label="Bio" name="bio" type="textarea" :value="$user->bio" placeholder="Tell us about yourself..." />
            <div class="flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary transition">Save Profile</button>
            </div>
        </form>
    </div>

    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-semibold text-white mb-6">Change Password</h3>
        <form method="POST" action="{{ route('profile.password') }}" class="space-y-5 max-w-md">
            @csrf
            @method('PUT')
            <x-form-input label="Current Password" name="current_password" type="password" required />
            <x-form-input label="New Password" name="password" type="password" required />
            <x-form-input label="Confirm Password" name="password_confirmation" type="password" required />
            <div class="flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary transition">Update Password</button>
            </div>
        </form>
    </div>
</div>
@endsection
