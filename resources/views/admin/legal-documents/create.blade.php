@extends('layouts.app')

@section('title', 'Create Legal Document')
@section('page-title', 'Create Legal Document')
@section('breadcrumb', 'Legal Documents / Create')

@section('content')
<div class="max-w-3xl space-y-6">
    <a href="{{ route('admin.legal-documents.index') }}" class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back
    </a>

    <div class="glass-card rounded-2xl p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.legal-documents.store') }}" class="space-y-5">
            @csrf
            <x-form-input label="Title" name="title" :value="old('title')" required />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-form-input label="Document Type" name="document_type" type="select" required>
                    @foreach([
                        'privacy_policy' => 'Privacy Policy',
                        'terms_of_service' => 'Terms of Service',
                        'refund_policy' => 'Refund Policy',
                        'user_agreement' => 'User Agreement',
                        'other' => 'Other',
                    ] as $value => $label)
                        <option value="{{ $value }}" @selected(old('document_type') === $value)>{{ $label }}</option>
                    @endforeach
                </x-form-input>
                <x-form-input label="Version" name="version" :value="old('version', '1.0')" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-form-input label="Effective Date" name="effective_date" type="date" :value="old('effective_date')" />
                <x-form-input label="Status" name="status" type="select" required>
                    @foreach(['draft' => 'Draft', 'published' => 'Published'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', 'draft') === $value)>{{ $label }}</option>
                    @endforeach
                </x-form-input>
            </div>
            <x-form-input label="Content" name="content" type="textarea" :value="old('content')" rows="8" />
            <div class="flex gap-3 pt-2">
                <button type="submit" class="panel-btn-primary">Create Document</button>
                <a href="{{ route('admin.legal-documents.index') }}" class="panel-btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
