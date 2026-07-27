@extends('layouts.app')

@section('title', 'Contacts')
@section('page-title', 'Contacts')
@section('breadcrumb', 'Users / Contacts')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-sm text-slate-500">Manage contact lists and communication records for your users.</p>
        <a href="{{ route('admin.contacts.create') }}" class="panel-btn-primary">Add Contact</a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($contacts->count())
        <div class="overflow-x-auto">
            <table id="contactsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Phone</th>
                        <th class="px-6 py-4">Organization</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contacts as $contact)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $contact->name }}</td>
                        <td class="px-6 py-4">{{ $contact->email ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $contact->phone ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $contact->organization ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $contact->typeLabel() }}</td>
                        <td class="px-6 py-4">{{ $contact->is_active ? 'Active' : 'Inactive' }}</td>
                        <td class="px-6 py-4">
                            <x-admin.table-actions
                                :edit-url="route('admin.contacts.edit', $contact)"
                                :delete-url="route('admin.contacts.destroy', $contact)"
                                edit-title="Edit contact"
                                delete-title="Delete contact"
                            />
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No contacts yet" description="Add contacts to track leads, customers, and partners." :action="route('admin.contacts.create')" actionLabel="Add Contact" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($contacts->count())
    <x-admin.datatable-scripts table-id="contactsTable" entity="contacts" :order-column="0" order-direction="asc" :action-column="6" export-file-name="contacts" />
@endif
@endpush
