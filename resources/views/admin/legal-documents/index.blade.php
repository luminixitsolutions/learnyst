@extends('layouts.app')

@section('title', 'Legal Documents')
@section('page-title', 'Legal Documents')
@section('breadcrumb', 'Users / Legal Documents')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-sm text-slate-500">Store and manage legal documents, policies, and agreements.</p>
        <a href="{{ route('admin.legal-documents.create') }}" class="panel-btn-primary">Add Document</a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($legalDocuments->count())
        <div class="overflow-x-auto">
            <table id="legalDocumentsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Version</th>
                        <th class="px-6 py-4">Effective Date</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($legalDocuments as $document)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $document->title }}</td>
                        <td class="px-6 py-4">{{ $document->typeLabel() }}</td>
                        <td class="px-6 py-4">{{ $document->version }}</td>
                        <td class="px-6 py-4 whitespace-nowrap" data-order="{{ optional($document->effective_date)->timestamp ?? 0 }}">
                            {{ $document->effective_date?->format('M d, Y') ?? '—' }}
                        </td>
                        <td class="px-6 py-4">{{ ucfirst($document->status) }}</td>
                        <td class="px-6 py-4">
                            <x-admin.table-actions
                                :edit-url="route('admin.legal-documents.edit', $document)"
                                :delete-url="route('admin.legal-documents.destroy', $document)"
                                edit-title="Edit document"
                                delete-title="Delete document"
                            />
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No legal documents yet" description="Add privacy policies, terms, refund policies, and agreements." :action="route('admin.legal-documents.create')" actionLabel="Add Document" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($legalDocuments->count())
    <x-admin.datatable-scripts table-id="legalDocumentsTable" entity="legal documents" :order-column="3" order-direction="desc" :action-column="5" export-file-name="legal-documents" />
@endif
@endpush
