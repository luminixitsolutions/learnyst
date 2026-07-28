@extends('layouts.app')

@section('title', 'AI Center')
@section('page-title', 'AI Center')
@section('breadcrumb', 'AI Center')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-2">Provider settings (OpenAI-compatible)</h3>
        <p class="text-sm text-slate-500 mb-4">API keys are encrypted at rest. Works with OpenAI, Azure OpenAI proxies, Groq, etc.</p>
        <form method="POST" action="{{ route('admin.ai.settings') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            <x-form-input label="Base URL" name="base_url" :value="$config['base_url']" placeholder="https://api.openai.com/v1" />
            <x-form-input label="Model" name="model" :value="$config['model']" />
            <x-form-input label="API Key" name="api_key" type="password" placeholder="{{ $config['api_key'] ? '•••••••• (leave blank to keep)' : 'sk-...' }}" />
            <label class="flex items-center gap-2 self-end pb-2">
                <input type="hidden" name="enabled" value="0">
                <input type="checkbox" name="enabled" value="1" @checked($config['enabled']) class="rounded border-slate-300 bg-white text-emerald-500">
                <span class="text-sm text-slate-600">AI enabled</span>
            </label>
            <div class="md:col-span-2"><button class="px-5 py-2.5 rounded-xl panel-btn-primary">Save settings</button></div>
        </form>
    </div>

    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Generate draft</h3>
        <form method="POST" action="{{ route('admin.ai.generate') }}" class="space-y-4">
            @csrf
            <x-form-input label="Feature" name="feature" type="select" required>
                @foreach($features as $key => $label)
                    @if(! in_array($key, ['doubt_chat'], true))
                    <option value="{{ $key }}">{{ $label }}</option>
                    @endif
                @endforeach
            </x-form-input>
            <x-form-input label="Title" name="title" />
            <x-form-input label="Prompt" name="prompt" type="textarea" required />
            <button class="px-5 py-2.5 rounded-xl panel-btn-primary">Generate draft</button>
        </form>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        <div class="px-6 py-4 border-b border-slate-200"><h3 class="font-bold text-slate-800">Drafts for review</h3></div>
        @if($drafts->count())
        <div class="overflow-x-auto">
            <table id="aiDraftsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Feature</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($drafts as $draft)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $draft->title }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $features[$draft->feature] ?? $draft->feature }}</td>
                        <td class="px-6 py-4"><x-badge type="info">{{ $draft->status }}</x-badge></td>
                        <td class="px-6 py-4"><a href="{{ route('admin.ai.show', $draft) }}" class="text-emerald-600 text-sm">Review</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No drafts yet." description="Generate a draft using the form above." />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($drafts->count())
    <x-admin.datatable-scripts table-id="aiDraftsTable" entity="drafts" :order-column="0" order-direction="desc" :action-column="3" export-file-name="ai-drafts" />
@endif
@endpush
