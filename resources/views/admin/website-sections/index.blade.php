@extends('layouts.app')

@section('title', 'Homepage Sections')
@section('page-title', 'Website Builder')
@section('breadcrumb', 'Website / Homepage Sections')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <p class="text-sm text-slate-500">Manage dynamic homepage sections for your academy website.</p>
        <div class="flex gap-2">
            <a href="{{ route('admin.website-sections.preview') }}" target="_blank" class="panel-btn-secondary">Preview Website</a>
            <a href="{{ route('admin.website-sections.create') }}" class="panel-btn-primary">Add Section</a>
        </div>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($sections->count())
        <div class="overflow-x-auto">
            <table id="websiteSectionsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Section</th>
                        <th class="px-6 py-4">Heading</th>
                        <th class="px-6 py-4">Order</th>
                        <th class="px-6 py-4">Visible</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sections as $section)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $section->name }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $section->heading ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $section->sort_order }}</td>
                        <td class="px-6 py-4"><x-badge :type="$section->is_visible ? 'success' : 'default'">{{ $section->is_visible ? 'Show' : 'Hidden' }}</x-badge></td>
                        <td class="px-6 py-4 text-right space-x-3">
                            <a href="{{ route('admin.website-sections.edit', $section) }}" class="text-indigo-600 text-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.website-sections.destroy', $section) }}" class="inline">@csrf @method('DELETE')
                                <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-red-500 text-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No homepage sections" description="Create your first section to build the academy homepage." />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($sections->count())
    <x-admin.datatable-scripts table-id="websiteSectionsTable" entity="sections" :order-column="2" order-direction="asc" :action-column="4" export-file-name="website-sections" />
@endif
@endpush
