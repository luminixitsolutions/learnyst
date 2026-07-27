@extends('layouts.app')

@section('title', 'Free Resources')
@section('page-title', 'Free Resources')
@section('breadcrumb', 'More Products / Free Resources')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <a href="{{ route('admin.more-products.index') }}" class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back
    </a>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-sm text-slate-500">Reach more potential learners with free resources.</p>
        <a href="{{ route('admin.resources.create') }}" class="panel-btn-primary">Create Resource</a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($resources->count())
        <div class="overflow-x-auto">
            <table id="resourcesTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Created</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($resources as $resource)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $resource->title }}</td>
                        <td class="px-6 py-4">{{ strtoupper($resource->resource_type) }}</td>
                        <td class="px-6 py-4">{{ ucfirst($resource->status) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap" data-order="{{ $resource->created_at->timestamp }}">{{ $resource->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <x-admin.table-actions
                                :edit-url="route('admin.resources.edit', $resource)"
                                :delete-url="route('admin.resources.destroy', $resource)"
                                edit-title="Edit resource"
                                delete-title="Delete resource"
                            />
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No resources yet" description="Add free PDFs, videos, links, and more for learner access." :action="route('admin.resources.create')" actionLabel="Create Resource" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($resources->count())
    <x-admin.datatable-scripts table-id="resourcesTable" entity="resources" :order-column="3" order-direction="desc" :action-column="4" export-file-name="resources" />
@endif
@endpush
