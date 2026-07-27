@extends('layouts.app')

@section('title', 'Instructors')
@section('page-title', 'Instructors')
@section('breadcrumb', 'Manage instructors')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-sm text-slate-500">Manage instructors, course assignments, and batch schedules.</p>
        <a href="{{ route('admin.instructors.create') }}" class="panel-btn-primary">Add Instructor</a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($instructors->count())
        <div class="overflow-x-auto">
            <table id="instructorsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Courses</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($instructors as $instructor)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.instructors.show', $instructor) }}" class="font-medium text-slate-800 hover:text-indigo-600">{{ $instructor->name }}</a>
                        </td>
                        <td class="px-6 py-4">{{ $instructor->email }}</td>
                        <td class="px-6 py-4">{{ $instructor->courses_count }}</td>
                        <td class="px-6 py-4">{{ $instructor->is_active ? 'Active' : 'Inactive' }}</td>
                        <td class="px-6 py-4">
                            <x-admin.table-actions
                                :edit-url="route('admin.instructors.edit', $instructor)"
                                :delete-url="route('admin.instructors.destroy', $instructor)"
                                edit-title="Edit instructor"
                                delete-title="Delete instructor"
                            />
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No instructors yet" :action="route('admin.instructors.create')" actionLabel="Add Instructor" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($instructors->count())
    <x-admin.datatable-scripts table-id="instructorsTable" entity="instructors" :order-column="0" order-direction="asc" :action-column="4" export-file-name="instructors" />
@endif
@endpush
