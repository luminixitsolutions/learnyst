@extends('layouts.app')

@section('title', 'Edit Learner')
@section('page-title', 'Edit Learner')
@section('breadcrumb', $learner->name)

@section('content')
<div class="max-w-2xl">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.learners.update', $learner) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')
            <x-form-input label="Full Name" name="name" :value="$learner->name" required />
            <x-form-input label="Email" name="email" type="email" :value="$learner->email" required />
            <x-form-input label="Mobile Number" name="phone" :value="$learner->phone" />
            <x-form-input label="New Password" name="password" type="password" placeholder="Leave blank to keep current" />
            <x-form-input label="Address" name="address" type="textarea" :value="$learner->address" />
            <x-form-input label="Notes" name="notes" type="textarea" :value="$learner->notes" />
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700">Profile Photo</label>
                @if($learner->avatar)
                    <img src="{{ Storage::url($learner->avatar) }}" alt="" class="w-16 h-16 rounded-full object-cover mb-2">
                @endif
                <input type="file" name="avatar" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-slate-900 file:text-white">
            </div>
            <div class="flex flex-wrap gap-6">
                <label class="flex items-center gap-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $learner->is_active)) class="rounded border-slate-300 text-indigo-600">
                    <span class="text-sm text-slate-700">Status: Active</span>
                </label>
                <label class="flex items-center gap-3">
                    <input type="hidden" name="email_verified" value="0">
                    <input type="checkbox" name="email_verified" value="1" @checked(old('email_verified', (bool) $learner->email_verified_at)) class="rounded border-slate-300 text-indigo-600">
                    <span class="text-sm text-slate-700">Email Verified</span>
                </label>
            </div>
            <div class="flex justify-between pt-4">
                <a href="{{ route('admin.learners.show', $learner) }}" class="px-4 py-2 text-sm text-slate-500 hover:text-slate-800">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
