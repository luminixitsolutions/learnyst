@extends('layouts.app')

@section('title', 'Learners')
@section('page-title', 'Learners')
@section('breadcrumb', 'Manage learners')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-sm text-slate-500">Manage learners, enrollments, and learner profiles.</p>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.learners.export') }}" class="panel-btn-secondary">Export CSV</a>
            <form method="POST" action="{{ route('admin.learners.import') }}" enctype="multipart/form-data" class="flex items-center gap-2">
                @csrf
                <input type="file" name="file" accept=".csv,.txt" required class="text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-600 file:font-medium">
                <button type="submit" class="panel-btn-secondary">Import</button>
            </form>
            <a href="{{ route('admin.learners.create') }}" class="panel-btn-primary">Add Learner</a>
        </div>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($learners->count())
        <div class="overflow-x-auto">
            <table id="learnersTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Phone</th>
                        <th class="px-6 py-4">Enrollments</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($learners as $learner)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.learners.show', $learner) }}" class="font-medium text-slate-800 hover:text-indigo-600">{{ $learner->name }}</a>
                        </td>
                        <td class="px-6 py-4">{{ $learner->email }}</td>
                        <td class="px-6 py-4">{{ $learner->phone ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $learner->enrollments_count }}</td>
                        <td class="px-6 py-4">{{ $learner->is_active ? 'Active' : 'Inactive' }}</td>
                        <td class="px-6 py-4">
                            <x-admin.table-actions
                                :edit-url="route('admin.learners.edit', $learner)"
                                :delete-url="route('admin.learners.destroy', $learner)"
                                edit-title="Edit learner"
                                delete-title="Delete learner"
                            />
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No learners found" description="Add your first learner to get started." :action="route('admin.learners.create')" actionLabel="Add Learner" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($learners->count())
    <x-admin.datatable-scripts table-id="learnersTable" entity="learners" :order-column="0" order-direction="asc" :action-column="5" export-file-name="learners" />
@endif
@endpush
