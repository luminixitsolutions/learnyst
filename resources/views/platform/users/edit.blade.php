@extends('layouts.app')

@section('title', 'Edit '.$user->name)
@section('page-title', 'Edit user')
@section('breadcrumb', 'Platform Admin / Users / Edit')

@section('content')
<div class="max-w-2xl space-y-6">
    <a href="{{ route('platform.users.show', $user) }}" class="text-sm text-slate-500 hover:text-slate-800">← Back to detail</a>

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('platform.users.update', $user) }}" class="glass-card rounded-2xl p-6 space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <x-form-input label="Name" name="name" :value="old('name', $user->name)" required />
            </div>
            <x-form-input label="Email" name="email" type="email" :value="old('email', $user->email)" required />
            <x-form-input label="Phone" name="phone" :value="old('phone', $user->phone)" />
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Role <span class="text-rose-500">*</span></label>
                <select name="role_id" class="panel-input w-full" required>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" @selected((string) old('role_id', $user->role_id) === (string) $role->id)>{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role_id')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Institute (optional)</label>
                <select name="company_id" class="panel-input w-full">
                    <option value="">None / platform</option>
                    @foreach($institutes as $institute)
                        <option value="{{ $institute->id }}" @selected((string) old('company_id', $currentInstituteId) === (string) $institute->id)>{{ $institute->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <x-form-input label="Notes" name="notes" type="textarea" :value="old('notes', $user->notes)" />
            </div>
        </div>

        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active)) class="rounded border-slate-300 text-teal-600">
            Active
        </label>

        @if($user->isSuperAdmin())
            <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                This is a super-admin account. Demotion and deactivation are blocked when it would leave the platform without an active super-admin, and you cannot demote/deactivate yourself.
            </p>
        @endif

        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('platform.users.show', $user) }}" class="panel-btn-secondary">Cancel</a>
            <button type="submit" class="panel-btn-primary">Save changes</button>
        </div>
    </form>
</div>
@endsection
