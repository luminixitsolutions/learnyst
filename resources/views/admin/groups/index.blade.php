@extends('layouts.app')

@section('title', 'Groups')
@section('page-title', 'Learner Groups')
@section('breadcrumb', 'Manage learner groups')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-sm text-slate-500">Organize learners into groups and assign shared courses.</p>
        <a href="{{ route('admin.groups.create') }}" class="panel-btn-primary">Create Group</a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($groups->count())
        <div class="overflow-x-auto">
            <table id="groupsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Group</th>
                        <th class="px-6 py-4">Learners</th>
                        <th class="px-6 py-4">Courses</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groups as $group)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.groups.show', $group) }}" class="font-medium text-slate-800 hover:text-indigo-600">{{ $group->name }}</a>
                            @if($group->description)
                                <p class="text-xs text-slate-500 truncate max-w-xs">{{ Str::limit($group->description, 60) }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $group->learners_count }}</td>
                        <td class="px-6 py-4">{{ $group->courses_count }}</td>
                        <td class="px-6 py-4">{{ $group->is_active ? 'Active' : 'Inactive' }}</td>
                        <td class="px-6 py-4">
                            <x-admin.table-actions
                                :edit-url="route('admin.groups.edit', $group)"
                                :delete-url="route('admin.groups.destroy', $group)"
                                edit-title="Edit group"
                                delete-title="Delete group"
                            />
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No groups yet" :action="route('admin.groups.create')" actionLabel="Create Group" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($groups->count())
    <x-admin.datatable-scripts table-id="groupsTable" entity="groups" :order-column="0" order-direction="asc" :action-column="4" export-file-name="groups" />
@endif
@endpush
