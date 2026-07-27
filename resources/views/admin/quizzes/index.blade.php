@extends('layouts.app')

@section('title', 'Quizzes')
@section('page-title', 'Quizzes')
@section('breadcrumb', 'Course Management / Quizzes')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-sm text-slate-500">Manage quiz lessons linked to your course sections.</p>
        <a href="{{ route('admin.quizzes.create') }}" class="panel-btn-primary">Create Quiz</a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($quizzes->count())
        <div class="overflow-x-auto">
            <table id="quizzesTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Quiz</th>
                        <th class="px-6 py-4">Course</th>
                        <th class="px-6 py-4">Section</th>
                        <th class="px-6 py-4">Marks</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quizzes as $quiz)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $quiz->title }}</td>
                        <td class="px-6 py-4">{{ $quiz->section?->course?->title ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $quiz->section?->title ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $quiz->quiz_data['total_marks'] ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.quizzes.edit', $quiz) }}"
                                   class="action-icon-btn action-icon-btn--edit"
                                   title="Edit quiz">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('admin.quizzes.destroy', $quiz) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            class="action-icon-btn action-icon-btn--delete"
                                            title="Delete quiz"
                                            @click="deleteForm = $el.closest('form'); deleteModal = true">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No quizzes yet" description="Create quiz lessons linked to course sections." :action="route('admin.quizzes.create')" actionLabel="Create Quiz" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($quizzes->count())
    <x-admin.datatable-scripts
        table-id="quizzesTable"
        entity="quizzes"
        :order-column="0"
        order-direction="desc"
        :action-column="4"
        export-file-name="quizzes"
    />
@endif
@endpush
