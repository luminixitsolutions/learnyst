@extends('layouts.app')

@section('title', 'Edit Sub Admin')
@section('page-title', 'Edit Sub Admin')
@section('breadcrumb', $subAdmin->name)

@section('content')
<div class="max-w-2xl">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.sub-admins.update', $subAdmin) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')
            <x-form-input label="Full Name" name="name" :value="old('name', $subAdmin->name)" required />
            <x-form-input label="Email" name="email" type="email" :value="old('email', $subAdmin->email)" required />
            <x-form-input label="Phone" name="phone" :value="old('phone', $subAdmin->phone)" />
            <x-form-input label="New Password" name="password" type="password" placeholder="Leave blank to keep current" />
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-300">Avatar</label>
                @if($subAdmin->avatar)
                    <img src="{{ Storage::url($subAdmin->avatar) }}" alt="" class="w-12 h-12 rounded-lg object-cover mb-2">
                @endif
                <input type="file" name="avatar" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-slate-800 file:text-slate-300">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach(['facebook', 'linkedin', 'twitter', 'website'] as $platform)
                <x-form-input :label="ucfirst($platform)" :name="'social_links['.$platform.']'" :value="old('social_links.'.$platform, $subAdmin->social_links[$platform] ?? '')" placeholder="https://" />
                @endforeach
            </div>
            <label class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $subAdmin->is_active)) class="rounded border-slate-600 bg-slate-800 text-brand-500">
                <span class="text-sm text-slate-300">Active</span>
            </label>
            <div class="flex justify-between pt-4 border-t border-slate-200">
                <a href="{{ route('admin.sub-admins.show', $subAdmin) }}" class="text-sm text-slate-500 hover:text-white">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
