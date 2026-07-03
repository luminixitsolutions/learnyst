@extends('layouts.app')

@section('title', $subAdmin->name)
@section('page-title', $subAdmin->name)
@section('breadcrumb', 'Sub Admins / Profile')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            @if($subAdmin->avatar)
                <img src="{{ Storage::url($subAdmin->avatar) }}" alt="" class="w-14 h-14 rounded-2xl object-cover">
            @else
                <div class="w-14 h-14 rounded-2xl bg-slate-800 flex items-center justify-center text-xl font-bold text-indigo-600">{{ strtoupper(substr($subAdmin->name, 0, 1)) }}</div>
            @endif
            <div>
                <div class="flex items-center gap-2">
                    <x-badge :type="$subAdmin->is_active ? 'success' : 'danger'">{{ $subAdmin->is_active ? 'Active' : 'Inactive' }}</x-badge>
                    <x-badge type="info">{{ $subAdmin->role?->name ?? 'Sub Admin' }}</x-badge>
                </div>
                <p class="text-slate-500 text-sm mt-1">{{ $subAdmin->email }}</p>
                @if($subAdmin->phone)<p class="text-slate-500 text-sm">{{ $subAdmin->phone }}</p>@endif
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.sub-admins.edit', $subAdmin) }}" class="px-4 py-2 rounded-xl bg-brand-600 text-white text-sm hover:bg-brand-500">Edit</a>
            <a href="{{ route('admin.sub-admins.index') }}" class="panel-btn-secondary">Back</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Role Permissions</h3>
            @if($subAdmin->role?->permissions->count())
            <div class="flex flex-wrap gap-2">
                @foreach($subAdmin->role->permissions as $permission)
                    <x-badge type="default">{{ $permission->name }}</x-badge>
                @endforeach
            </div>
            @else
            <p class="text-sm text-slate-500">No permissions assigned to role</p>
            @endif
        </div>

        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Scoped Access</h3>
            @php
                $scopes = $subAdmin->subAdminScopes->groupBy('scope_type');
            @endphp
            @forelse($scopes as $type => $items)
                <p class="text-xs text-slate-500 uppercase tracking-wide mb-2">{{ class_basename($type) }}s ({{ $items->count() }})</p>
                <div class="mb-4 space-y-1">
                    @foreach($items as $scope)
                        <p class="text-sm text-slate-500">ID: {{ $scope->scope_id }}</p>
                    @endforeach
                </div>
            @empty
                <p class="text-sm text-slate-500">Full access (no scope restrictions)</p>
            @endforelse
        </div>
    </div>

    @if($subAdmin->social_links && count(array_filter($subAdmin->social_links)))
    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Social Links</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($subAdmin->social_links as $platform => $url)
                @if($url)
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-slate-500 capitalize">{{ $platform }}:</span>
                    <a href="{{ $url }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 truncate">{{ $url }}</a>
                </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
