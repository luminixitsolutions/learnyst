@extends('layouts.app')

@section('title', 'Checkout Consents')
@section('page-title', 'Checkout Consents')
@section('breadcrumb', 'Manage checkout consent forms')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <x-page-header title="Consent Forms">
        <p class="text-sm text-slate-500 mt-1">{{ $totalAcceptances }} total acceptances</p>
    </x-page-header>

    <div class="flex flex-wrap gap-2 justify-end">
        <a href="{{ route('admin.checkout-consents.report') }}" class="panel-btn-secondary">View Report</a>
        <a href="{{ route('admin.checkout-consents.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl panel-btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Consent
        </a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($consents->count())
        <div class="overflow-x-auto">
            <table id="consentsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4 font-medium">Title</th>
                        <th class="px-6 py-4 font-medium">Required</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Sort</th>
                        <th class="px-6 py-4 font-medium">Acceptances</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($consents as $consent)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4">
                            <p class="text-slate-800 font-semibold">{{ $consent->title }}</p>
                            @if($consent->description)<p class="text-xs text-slate-500">{{ Str::limit($consent->description, 50) }}</p>@endif
                        </td>
                        <td class="px-6 py-4"><x-badge :type="$consent->is_required ? 'warning' : 'default'">{{ $consent->is_required ? 'Required' : 'Optional' }}</x-badge></td>
                        <td class="px-6 py-4"><x-badge :type="$consent->is_active ? 'success' : 'danger'">{{ $consent->is_active ? 'Active' : 'Inactive' }}</x-badge></td>
                        <td class="px-6 py-4 text-slate-500">{{ $consent->sort_order ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $consent->order_consents_count }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.checkout-consents.edit', $consent) }}" class="text-indigo-600 hover:text-indigo-800 text-sm mr-3">Edit</a>
                            <form method="POST" action="{{ route('admin.checkout-consents.destroy', $consent) }}" class="inline">@csrf @method('DELETE')
                                <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-red-500 hover:text-red-700 text-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No consent forms" description="Add consent forms to display during checkout." :action="route('admin.checkout-consents.create')" actionLabel="Add Consent" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($consents->count())
    <x-admin.datatable-scripts table-id="consentsTable" entity="consent forms" :order-column="3" order-direction="asc" :action-column="5" export-file-name="checkout-consents" />
@endif
@endpush
