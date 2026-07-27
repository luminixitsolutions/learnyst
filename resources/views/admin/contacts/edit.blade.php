@extends('layouts.app')

@section('title', 'Edit Contact')
@section('page-title', 'Edit Contact')
@section('breadcrumb', 'Contacts / Edit')

@section('content')
<div class="max-w-3xl space-y-6">
    <a href="{{ route('admin.contacts.index') }}" class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back
    </a>

    <div class="glass-card rounded-2xl p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.contacts.update', $contact) }}" class="space-y-5">
            @csrf
            @method('PUT')
            <x-form-input label="Name" name="name" :value="old('name', $contact->name)" required />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-form-input label="Email" name="email" type="email" :value="old('email', $contact->email)" />
                <x-form-input label="Phone" name="phone" :value="old('phone', $contact->phone)" />
            </div>
            <x-form-input label="Organization" name="organization" :value="old('organization', $contact->organization)" />
            <x-form-input label="Contact Type" name="contact_type" type="select" required>
                @foreach(['lead' => 'Lead', 'customer' => 'Customer', 'partner' => 'Partner', 'vendor' => 'Vendor'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('contact_type', $contact->contact_type) === $value)>{{ $label }}</option>
                @endforeach
            </x-form-input>
            <x-form-input label="Notes" name="notes" type="textarea" :value="old('notes', $contact->notes)" />
            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $contact->is_active)) class="rounded border-slate-300 text-indigo-600">
                Active
            </label>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="panel-btn-primary">Save Changes</button>
                <a href="{{ route('admin.contacts.index') }}" class="panel-btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
