@extends('layouts.app')

@section('title', 'Sub Admins')
@section('page-title', 'Sub Admins')
@section('breadcrumb', 'Manage sub-administrators')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search name or email..."
                   class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
            <button type="submit" class="panel-btn-secondary hover:bg-slate-700">Filter</button>
        </form>
        <a href="{{ route('admin.sub-admins.wizard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl panel-btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Sub Admin
        </a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($subAdmins->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                        <th class="px-6 py-4 font-medium">Name</th>
                        <th class="px-6 py-4 font-medium">Email</th>
                        <th class="px-6 py-4 font-medium">Role</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subAdmins as $subAdmin)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.sub-admins.show', $subAdmin) }}" class="text-slate-800 font-semibold hover:text-indigo-600">{{ $subAdmin->name }}</a>
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $subAdmin->email }}</td>
                        <td class="px-6 py-4 text-slate-300">{{ $subAdmin->role?->name ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <form method="POST" action="{{ route('admin.sub-admins.toggle', $subAdmin) }}" class="inline">@csrf
                                <button type="submit" class="inline"><x-badge :type="$subAdmin->is_active ? 'success' : 'danger'">{{ $subAdmin->is_active ? 'Active' : 'Inactive' }}</x-badge></button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.sub-admins.edit', $subAdmin) }}" class="text-indigo-600 hover:text-indigo-800 text-sm mr-3">Edit</a>
                            <form method="POST" action="{{ route('admin.sub-admins.destroy', $subAdmin) }}" class="inline">@csrf @method('DELETE')
                                <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-red-500 hover:text-red-700 text-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $subAdmins->links() }}</div>
        @else
        <x-empty-state title="No sub-admins yet" :action="route('admin.sub-admins.wizard')" actionLabel="Add Sub Admin" />
        @endif
    </div>
</div>
@endsection
