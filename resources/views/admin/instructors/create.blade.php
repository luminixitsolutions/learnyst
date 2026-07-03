@extends('layouts.app')

@section('title', 'Add Instructor')
@section('page-title', 'Add Instructor')
@section('breadcrumb', 'Instructors / Create')

@section('content')
<div class="max-w-2xl">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.instructors.store') }}" class="space-y-5">
            @csrf
            <x-form-input label="Full Name" name="name" required />
            <x-form-input label="Email" name="email" type="email" required />
            <x-form-input label="Phone" name="phone" />
            <x-form-input label="Bio" name="bio" type="textarea" />
            <x-form-input label="Password" name="password" type="password" required />
            <div class="flex justify-between pt-4">
                <a href="{{ route('admin.instructors.index') }}" class="text-sm text-slate-500">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Create Instructor</button>
            </div>
        </form>
    </div>
</div>
@endsection
