@extends('layouts.app')

@section('title', 'Learners')
@section('page-title', 'Learners')
@section('breadcrumb', 'Manage learners')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search name, email, phone..."
                   class="panel-input min-w-[220px]">
            <select name="status" class="panel-select">
                <option value="">All Status</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
            </select>
            <button type="submit" class="panel-btn-secondary">Filter</button>
        </form>
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

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($learners->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
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
                    <tr>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.learners.show', $learner) }}" class="text-slate-800 font-semibold hover:text-indigo-600 transition">{{ $learner->name }}</a>
                        </td>
                        <td class="px-6 py-4">{{ $learner->email }}</td>
                        <td class="px-6 py-4">{{ $learner->phone ?? '—' }}</td>
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $learner->enrollments_count }}</td>
                        <td class="px-6 py-4"><x-badge :type="$learner->is_active ? 'success' : 'danger'">{{ $learner->is_active ? 'Active' : 'Inactive' }}</x-badge></td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <a href="{{ route('admin.learners.edit', $learner) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium mr-3">Edit</a>
                            <form method="POST" action="{{ route('admin.learners.destroy', $learner) }}" class="inline">@csrf @method('DELETE')
                                <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-red-500 hover:text-red-700 text-sm font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">{{ $learners->links() }}</div>
        @else
        <x-empty-state title="No learners found" description="Add your first learner or adjust your search filters." :action="route('admin.learners.create')" actionLabel="Add Learner" />
        @endif
    </div>
</div>
@endsection
