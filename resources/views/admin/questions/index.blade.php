@extends('layouts.app')

@section('title', 'All Questions')
@section('page-title', 'All Questions')
@section('breadcrumb', 'Products')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div>
        <h3 class="text-2xl font-bold text-slate-900">All Questions</h3>
        <p class="text-sm text-slate-500 mt-1">All questions created in your school will be listed here</p>
    </div>

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <form method="GET" class="flex flex-1 max-w-lg items-center gap-2">
            <div class="relative flex-1">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search by Question Details"
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
            </div>
            @foreach(['question_type', 'status', 'date_from', 'date_to', 'sort'] as $field)
                @if(request($field))
                    <input type="hidden" name="{{ $field }}" value="{{ request($field) }}">
                @endif
            @endforeach
        </form>
        <div class="flex flex-wrap items-center gap-2 shrink-0">
            <button type="button" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs font-medium text-slate-600 hover:bg-slate-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Filters
            </button>
            <button type="button" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs font-medium text-slate-600 hover:bg-slate-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                Columns
            </button>
            <button type="button" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs font-medium text-slate-600 hover:bg-slate-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Date range
            </button>
            <button type="button" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs font-medium text-slate-600 hover:bg-slate-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export
            </button>
        </div>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($questions->count())
        <div class="overflow-x-auto">
            <table id="questionsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4 font-medium">Question</th>
                        <th class="px-6 py-4 font-medium">Type</th>
                        <th class="px-6 py-4 font-medium">Question Pool</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Created</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($questions as $question)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 max-w-md">
                            <p class="text-slate-800 font-medium line-clamp-2">{{ $question->question_text }}</p>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $question->typeLabel() }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $question->questionPool?->title ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <x-badge :type="match($question->status) { 'published' => 'success', 'draft' => 'warning', default => 'default' }">
                                {{ ucfirst($question->status) }}
                            </x-badge>
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $question->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-red-500 hover:text-red-700 text-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="py-20 px-6 text-center">
            <div class="w-24 h-24 mx-auto mb-6 text-slate-300">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    <circle cx="10" cy="10" r="6" stroke-width="1.5"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800">No results found</h3>
            <p class="text-sm text-slate-500 mt-2 max-w-md mx-auto">Questions from mock tests, quizzes, and question pools will appear here once created.</p>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($questions->count())
    <x-admin.datatable-scripts table-id="questionsTable" entity="questions" :order-column="4" order-direction="desc" :action-column="5" export-file-name="questions" />
@endif
@endpush
