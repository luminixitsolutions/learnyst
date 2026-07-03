@extends('layouts.app')

@section('title', 'Edit Role — '.$role->name)
@section('page-title', 'Edit Role')
@section('breadcrumb', 'Roles / '.$role->name)

@section('content')
<div class="max-w-4xl space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-form-input label="Role Name" name="name" :value="old('name', $role->name)" required />
                <x-form-input label="Description" name="description" :value="old('description', $role->description)" />
            </div>

            @if($role->is_system && $role->slug === 'admin')
            <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 text-sm">
                System admin role permissions cannot be modified.
            </div>
            @else
            <div class="space-y-6">
                <h3 class="text-lg font-bold text-slate-800">Permissions by Module</h3>
                @foreach($permissions as $module => $modulePermissions)
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-bold text-slate-800">{{ $modules[$module] ?? ucfirst(str_replace('_', ' ', $module)) }}</h4>
                        <span class="text-xs text-slate-500">{{ $modulePermissions->count() }} permissions</span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        @foreach($modulePermissions as $permission)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                   @checked(in_array($permission->id, old('permissions', $rolePermissions)))
                                   class="rounded border-slate-600 bg-slate-800 text-brand-500">
                            <span class="text-sm text-slate-300 capitalize">{{ $permission->action }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <div class="flex justify-between pt-4 border-t border-slate-200">
                <a href="{{ route('admin.roles.index') }}" class="text-sm text-slate-500 hover:text-white">Cancel</a>
                @unless($role->is_system && $role->slug === 'admin')
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Save Permissions</button>
                @endunless
            </div>
        </form>
    </div>
</div>
@endsection
