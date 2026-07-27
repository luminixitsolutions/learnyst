@extends('layouts.app')

@section('title', 'Bundles')
@section('page-title', 'Course Bundles')
@section('breadcrumb', 'Manage product bundles')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-sm text-slate-500">Create and manage course bundles for your institute.</p>
        <a href="{{ route('admin.bundles.create') }}" class="panel-btn-primary">Create Bundle</a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($bundles->count())
        <div class="overflow-x-auto">
            <table id="bundlesTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Bundle</th>
                        <th class="px-6 py-4">Courses</th>
                        <th class="px-6 py-4">Price</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bundles as $bundle)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4">
                            <p class="font-medium text-slate-800">{{ $bundle->title }}</p>
                            <p class="text-xs text-slate-500">{{ $bundle->created_at->format('M d, Y') }}</p>
                        </td>
                        <td class="px-6 py-4">{{ $bundle->courses_count }}</td>
                        <td class="px-6 py-4">₹{{ number_format($bundle->price ?? 0, 0) }}</td>
                        <td class="px-6 py-4">{{ ucfirst($bundle->status) }}</td>
                        <td class="px-6 py-4">
                            <x-admin.table-actions
                                :edit-url="route('admin.bundles.edit', $bundle)"
                                :delete-url="route('admin.bundles.destroy', $bundle)"
                                edit-title="Edit bundle"
                                delete-title="Delete bundle"
                            />
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No bundles yet" description="Create bundles to sell multiple courses together." :action="route('admin.bundles.create')" actionLabel="Create Bundle" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($bundles->count())
    <x-admin.datatable-scripts table-id="bundlesTable" entity="bundles" :order-column="0" order-direction="desc" :action-column="4" export-file-name="bundles" />
@endif
@endpush
