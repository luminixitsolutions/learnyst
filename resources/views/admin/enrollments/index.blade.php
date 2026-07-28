@extends('layouts.app')

@section('title', 'Enrollments')
@section('page-title', 'Enrollments')
@section('breadcrumb', 'Manage learner enrollments')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6" x-data="{ assignModal: false, enrollmentType: '{{ old('enrollment_type', 'course') }}' }">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search learner..."
                   class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
            <select name="type" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm focus:outline-none">
                <option value="">All Types</option>
                @foreach(['course' => 'Course', 'batch' => 'Batch', 'bundle' => 'Bundle'] as $val => $label)
                    <option value="{{ $val }}" @selected(request('type') === $val)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="status" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm focus:outline-none">
                <option value="">All Status</option>
                @foreach(['active', 'expired', 'revoked'] as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
            <button type="submit" class="panel-btn-secondary hover:bg-slate-700">Filter</button>
        </form>
        <button type="button" @click="assignModal = true" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl panel-btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Assign Enrollment
        </button>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($enrollments->count())
        <div class="overflow-x-auto">
            <table id="enrollmentsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4 font-medium">Learner</th>
                        <th class="px-6 py-4 font-medium">Type</th>
                        <th class="px-6 py-4 font-medium">Target</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Access Period</th>
                        <th class="px-6 py-4 font-medium">Enrolled</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($enrollments as $enrollment)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.enrollments.history', $enrollment->user) }}" class="text-slate-800 font-semibold hover:text-indigo-600">{{ $enrollment->user?->name }}</a>
                            <p class="text-xs text-slate-500">{{ $enrollment->user?->email }}</p>
                        </td>
                        <td class="px-6 py-4"><x-badge type="info">{{ ucfirst($enrollment->enrollment_type) }}</x-badge></td>
                        <td class="px-6 py-4 text-slate-600">
                            @if($enrollment->enrollment_type === 'course')
                                {{ $enrollment->course?->title ?? '—' }}
                            @elseif($enrollment->enrollment_type === 'batch')
                                {{ $enrollment->batch?->title ?? '—' }}
                            @else
                                {{ $enrollment->bundle?->title ?? '—' }}
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <x-badge :type="match($enrollment->status) { 'active' => 'success', 'expired' => 'warning', 'revoked' => 'danger', default => 'default' }">{{ ucfirst($enrollment->status) }}</x-badge>
                        </td>
                        <td class="px-6 py-4 text-slate-500 text-xs">
                            {{ $enrollment->access_starts_at?->format('M d, Y') ?? '—' }}
                            @if($enrollment->expires_at)
                                <span class="text-slate-600">→</span> {{ $enrollment->expires_at->format('M d, Y') }}
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $enrollment->enrolled_at?->format('M d, Y') ?? '—' }}</td>
                        <td class="px-6 py-4 text-right">
                            <form method="POST" action="{{ route('admin.enrollments.destroy', $enrollment) }}" class="inline">@csrf @method('DELETE')
                                <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-red-500 hover:text-red-700 text-sm">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No enrollments found" description="Assign enrollments to learners to get started." />
        @endif
    </div>

    {{-- Assign Enrollment Modal --}}
    <div x-show="assignModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="assignModal = false">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="assignModal = false"></div>
        <div class="relative w-full max-w-2xl glass-card rounded-2xl p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-slate-800">Assign Enrollment</h3>
                <button type="button" @click="assignModal = false" class="text-slate-500 hover:text-slate-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.enrollments.store') }}" class="space-y-5">
                @csrf
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-600">Learners <span class="text-red-500">*</span></label>
                    <select name="user_ids[]" multiple required size="6"
                            class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
                        @foreach($learners as $learner)
                            <option value="{{ $learner->id }}" @selected(in_array($learner->id, old('user_ids', [])))>{{ $learner->name }} ({{ $learner->email }})</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-500">Hold Ctrl/Cmd to select multiple learners</p>
                    @error('user_ids')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-600">Enrollment Type <span class="text-red-500">*</span></label>
                    <select name="enrollment_type" x-model="enrollmentType" required
                            class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
                        <option value="course">Course</option>
                        <option value="batch">Batch</option>
                        <option value="bundle">Bundle</option>
                    </select>
                </div>

                <div x-show="enrollmentType === 'course'" x-cloak>
                    <x-form-input label="Course" name="course_id" type="select">
                        <option value="">Select course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" @selected(old('course_id') == $course->id)>{{ $course->title }}</option>
                        @endforeach
                    </x-form-input>
                </div>
                <div x-show="enrollmentType === 'batch'" x-cloak>
                    <x-form-input label="Batch" name="batch_id" type="select">
                        <option value="">Select batch</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}" @selected(old('batch_id') == $batch->id)>{{ $batch->title }}</option>
                        @endforeach
                    </x-form-input>
                </div>
                <div x-show="enrollmentType === 'bundle'" x-cloak>
                    <x-form-input label="Bundle" name="bundle_id" type="select">
                        <option value="">Select bundle</option>
                        @foreach($bundles as $bundle)
                            <option value="{{ $bundle->id }}" @selected(old('bundle_id') == $bundle->id)>{{ $bundle->title }}</option>
                        @endforeach
                    </x-form-input>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-form-input label="Access Start Date" name="access_starts_at" type="date" :value="old('access_starts_at', now()->format('Y-m-d'))" />
                    <x-form-input label="Access Expiry Date" name="expires_at" type="date" :value="old('expires_at')" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-form-input label="Course Quiz Score" name="course_quiz_score" type="number" step="0.01" :value="old('course_quiz_score')" />
                    <x-form-input label="Mock Test Score" name="mock_test_score" type="number" step="0.01" :value="old('mock_test_score')" />
                    <x-form-input label="Test Series Score" name="test_series_score" type="number" step="0.01" :value="old('test_series_score')" />
                    <x-form-input label="Bundle Quiz Score" name="bundle_quiz_score" type="number" step="0.01" :value="old('bundle_quiz_score')" />
                </div>

                <x-form-input label="Status" name="status" type="select" required>
                    @foreach(['active', 'expired', 'revoked'] as $st)
                        <option value="{{ $st }}" @selected(old('status', 'active') === $st)>{{ ucfirst($st) }}</option>
                    @endforeach
                </x-form-input>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                    <button type="button" @click="assignModal = false" class="px-4 py-2 rounded-xl text-sm text-slate-500 hover:text-slate-800">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($enrollments->count())
    <x-admin.datatable-scripts table-id="enrollmentsTable" entity="enrollments" :order-column="0" order-direction="desc" :action-column="6" export-file-name="enrollments" />
@endif
@endpush
