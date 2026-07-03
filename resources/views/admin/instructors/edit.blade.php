@extends('layouts.app')

@section('title', 'Edit Instructor')
@section('page-title', 'Edit Instructor')
@section('breadcrumb', $instructor->name)

@section('content')
<div class="max-w-2xl">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.instructors.update', $instructor) }}" class="space-y-5">
            @csrf
            @method('PUT')
            <x-form-input label="Full Name" name="name" :value="$instructor->name" required />
            <x-form-input label="Email" name="email" type="email" :value="$instructor->email" required />
            <x-form-input label="Phone" name="phone" :value="$instructor->phone" />
            <x-form-input label="Bio" name="bio" type="textarea" :value="$instructor->bio" />
            <label class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $instructor->is_active ?? true)) class="rounded border-slate-600 bg-slate-800 text-brand-500">
                <span class="text-sm text-slate-300">Active account</span>
            </label>
            <div class="flex justify-between pt-4">
                <a href="{{ route('admin.instructors.show', $instructor) }}" class="text-sm text-slate-500">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
