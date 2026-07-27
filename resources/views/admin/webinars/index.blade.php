@extends('layouts.app')

@section('title', 'Webinars')
@section('page-title', 'Webinars')
@section('breadcrumb', 'More Products / Webinars')

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
        <p class="text-sm text-slate-500">Manage recorded and live webinar products.</p>
        <a href="{{ route('admin.webinars.create') }}" class="panel-btn-primary">Create Webinar</a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($webinars->count())
        <div class="overflow-x-auto">
            <table id="webinarsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Price</th>
                        <th class="px-6 py-4">Security</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Created</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($webinars as $webinar)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $webinar->title }}</td>
                        <td class="px-6 py-4">{{ $webinar->is_free ? 'Free' : '₹'.number_format($webinar->price, 0) }}</td>
                        <td class="px-6 py-4">{{ $webinar->contentSecurityLabel() }}</td>
                        <td class="px-6 py-4">{{ ucfirst($webinar->status) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap" data-order="{{ $webinar->created_at->timestamp }}">{{ $webinar->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <x-admin.table-actions :delete-url="route('admin.webinars.destroy', $webinar)" delete-title="Delete webinar" />
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No webinars yet" :action="route('admin.webinars.create')" actionLabel="Create Webinar" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($webinars->count())
    <x-admin.datatable-scripts table-id="webinarsTable" entity="webinars" :order-column="4" order-direction="desc" :action-column="5" export-file-name="webinars" />
@endif
@endpush
