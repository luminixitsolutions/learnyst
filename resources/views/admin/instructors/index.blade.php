@extends('layouts.app')

@section('title', 'Instructors')
@section('page-title', 'Instructors')
@section('breadcrumb', 'Manage instructors')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <form method="GET">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search instructors..."
                   class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm">
        </form>
        <a href="{{ route('admin.instructors.create') }}" class="px-5 py-2.5 rounded-xl panel-btn-primary">Add Instructor</a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($instructors->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
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
                        <td class="px-6 py-4"><a href="{{ route('admin.instructors.show', $instructor) }}" class="text-slate-800 font-semibold hover:text-indigo-600">{{ $instructor->name }}</a></td>
                        <td class="px-6 py-4 text-slate-500">{{ $instructor->email }}</td>
                        <td class="px-6 py-4 text-white">{{ $instructor->courses_count }}</td>
                        <td class="px-6 py-4"><x-badge :type="$instructor->is_active ? 'success' : 'danger'">{{ $instructor->is_active ? 'Active' : 'Inactive' }}</x-badge></td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.instructors.edit', $instructor) }}" class="text-indigo-600 text-sm mr-3">Edit</a>
                            <form method="POST" action="{{ route('admin.instructors.destroy', $instructor) }}" class="inline">@csrf @method('DELETE')
                                <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-red-400 text-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $instructors->links() }}</div>
        @else
        <x-empty-state title="No instructors yet" :action="route('admin.instructors.create')" actionLabel="Add Instructor" />
        @endif
    </div>
</div>
@endsection
