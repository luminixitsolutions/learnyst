@extends('layouts.app')

@section('title', 'Communities')
@section('page-title', 'Communities')
@section('breadcrumb', 'Manage communities')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex justify-end">
        <a href="{{ route('admin.communities.create') }}" class="px-5 py-2.5 rounded-xl panel-btn-primary">Create Community</a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($communities->count())
        <div class="overflow-x-auto">
            <table id="communitiesTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Community</th>
                        <th class="px-6 py-4">Members</th>
                        <th class="px-6 py-4">Posts</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($communities as $community)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4"><a href="{{ route('admin.communities.show', $community) }}" class="text-slate-800 font-semibold hover:text-indigo-600">{{ $community->name }}</a></td>
                        <td class="px-6 py-4 text-slate-500">{{ $community->members_count }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $community->posts_count }}</td>
                        <td class="px-6 py-4"><x-badge :type="$community->is_active ? 'success' : 'danger'">{{ $community->is_active ? 'Active' : 'Inactive' }}</x-badge></td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.communities.edit', $community) }}" class="text-indigo-600 text-sm mr-3">Edit</a>
                            <form method="POST" action="{{ route('admin.communities.destroy', $community) }}" class="inline">@csrf @method('DELETE')
                                <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-red-500 text-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No communities yet" :action="route('admin.communities.create')" actionLabel="Create Community" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($communities->count())
    <x-admin.datatable-scripts table-id="communitiesTable" entity="communities" :order-column="0" order-direction="desc" :action-column="4" export-file-name="communities" />
@endif
@endpush
