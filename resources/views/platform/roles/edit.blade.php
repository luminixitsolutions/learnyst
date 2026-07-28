@extends('layouts.app')

@section('title', 'Edit Role — '.$role->name)
@section('page-title', 'Edit Role')
@section('breadcrumb', 'Platform Admin / System / Roles / '.$role->name)

@section('content')
<div class="max-w-4xl space-y-6">
    <a href="{{ route('platform.roles.index') }}" class="text-sm text-slate-500 hover:text-slate-800">← All roles</a>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
    @endif

    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('platform.roles.update', $role) }}" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-slate-700">Role Name</label>
                    <input type="text" name="name" value="{{ old('name', $role->name) }}" @disabled($role->is_system) required class="panel-input w-full {{ $role->is_system ? 'bg-slate-50' : '' }}">
                    @if($role->is_system)
                        <input type="hidden" name="name" value="{{ $role->name }}">
                    @endif
                </div>
                <x-form-input label="Description" name="description" :value="old('description', $role->description)" />
            </div>

            @if($locked)
                <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-sm">
                    System role <strong>{{ $role->slug }}</strong> permissions cannot be modified from the platform panel.
                </div>
            @else
                <div class="space-y-6">
                    <h3 class="text-lg font-bold text-slate-800">Permissions by module</h3>
                    @forelse($permissions as $module => $modulePermissions)
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-bold text-slate-800">{{ $modules[$module] ?? ucfirst(str_replace('_', ' ', $module)) }}</h4>
                                <span class="text-xs text-slate-500">{{ $modulePermissions->count() }}</span>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                @foreach($modulePermissions as $permission)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                               @checked(in_array($permission->id, old('permissions', $rolePermissions)))
                                               class="rounded border-slate-300 text-brand-600">
                                        <span class="text-sm text-slate-600 capitalize">{{ $permission->action }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No permissions yet. Sync the catalog from the roles list.</p>
                    @endforelse
                </div>
            @endif

            <div class="flex justify-end pt-4 border-t border-slate-200">
                @unless($locked)
                    <button type="submit" class="panel-btn-primary">Save Role</button>
                @endunless
            </div>
        </form>
    </div>
</div>
@endsection
