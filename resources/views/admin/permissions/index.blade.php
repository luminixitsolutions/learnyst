@extends('layouts.app')

@section('title', 'Permissions')
@section('page-title', 'System Permissions')
@section('breadcrumb', 'All registered permissions')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <p class="text-sm text-slate-500">{{ $permissions->total() }} permissions across {{ count($modules) }} modules</p>
        <form method="POST" action="{{ route('admin.permissions.seed') }}">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl panel-btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Seed Permissions
            </button>
        </form>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($permissions->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                        <th class="px-6 py-4 font-medium">Name</th>
                        <th class="px-6 py-4 font-medium">Slug</th>
                        <th class="px-6 py-4 font-medium">Module</th>
                        <th class="px-6 py-4 font-medium">Action</th>
                        <th class="px-6 py-4 font-medium">Group</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $permission)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 text-white">{{ $permission->name }}</td>
                        <td class="px-6 py-4 text-slate-500 font-mono text-xs">{{ $permission->slug }}</td>
                        <td class="px-6 py-4"><x-badge type="info">{{ $modules[$permission->module] ?? $permission->module }}</x-badge></td>
                        <td class="px-6 py-4 text-slate-300 capitalize">{{ $permission->action }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $permission->group ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $permissions->links() }}</div>
        @else
        <div class="py-8 text-center">
            <x-empty-state title="No permissions found" description="Click Seed Permissions above to generate the default permission set." />
        </div>
        @endif
    </div>
</div>
@endsection
