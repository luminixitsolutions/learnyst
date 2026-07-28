@extends('layouts.app')

@section('title', 'Create user')
@section('page-title', 'Create user')
@section('breadcrumb', 'Platform Admin / Users / Create')

@section('content')
<div class="max-w-2xl space-y-6">
    <a href="{{ route('platform.users.index') }}" class="text-sm text-slate-500 hover:text-slate-800">← All users</a>

    <form method="POST" action="{{ route('platform.users.store') }}" class="glass-card rounded-2xl p-6 space-y-5">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <x-form-input label="Name" name="name" :value="old('name')" required />
            </div>
            <x-form-input label="Email" name="email" type="email" :value="old('email')" required />
            <x-form-input label="Phone" name="phone" :value="old('phone')" />
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Role <span class="text-rose-500">*</span></label>
                <select name="role_id" class="panel-input w-full" required>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" @selected((string) old('role_id') === (string) $role->id)>{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role_id')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Institute (optional)</label>
                <select name="company_id" class="panel-input w-full">
                    <option value="">None / platform</option>
                    @foreach($institutes as $institute)
                        <option value="{{ $institute->id }}" @selected((string) old('company_id') === (string) $institute->id)>{{ $institute->name }}</option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-400 mt-1">Links staff/learners under the institute owner.</p>
            </div>
            <x-form-input label="Password" name="password" type="password" required />
            <x-form-input label="Confirm password" name="password_confirmation" type="password" required />
        </div>
        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="rounded border-slate-300 text-teal-600">
            Active
        </label>
        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('platform.users.index') }}" class="panel-btn-secondary">Cancel</a>
            <button type="submit" class="panel-btn-primary">Create user</button>
        </div>
    </form>
</div>
@endsection
