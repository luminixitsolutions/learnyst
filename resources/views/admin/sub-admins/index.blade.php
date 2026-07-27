@extends('layouts.app')

@section('title', 'Sub Admins')
@section('page-title', 'Sub Admins')
@section('breadcrumb', 'Manage sub-administrators')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-sm text-slate-500">Manage sub-administrators with scoped access to courses and products.</p>
        <a href="{{ route('admin.sub-admins.wizard') }}" class="panel-btn-primary">Add Sub Admin</a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($subAdmins->count())
        <div class="overflow-x-auto">
            <table id="subAdminsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subAdmins as $subAdmin)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.sub-admins.show', $subAdmin) }}" class="font-medium text-slate-800 hover:text-indigo-600">{{ $subAdmin->name }}</a>
                        </td>
                        <td class="px-6 py-4">{{ $subAdmin->email }}</td>
                        <td class="px-6 py-4">{{ $subAdmin->role?->name ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $subAdmin->is_active ? 'Active' : 'Inactive' }}</td>
                        <td class="px-6 py-4">
                            <x-admin.table-actions
                                :edit-url="route('admin.sub-admins.edit', $subAdmin)"
                                :delete-url="route('admin.sub-admins.destroy', $subAdmin)"
                                edit-title="Edit sub admin"
                                delete-title="Delete sub admin"
                            />
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No sub-admins yet" :action="route('admin.sub-admins.wizard')" actionLabel="Add Sub Admin" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($subAdmins->count())
    <x-admin.datatable-scripts table-id="subAdminsTable" entity="sub admins" :order-column="0" order-direction="asc" :action-column="4" export-file-name="sub-admins" />
@endif
@endpush
