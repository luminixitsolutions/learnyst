@extends('layouts.app')

@section('title', 'Edit Assignment')
@section('page-title', 'Edit Assignment')
@section('breadcrumb', 'Assignments / Edit')

@section('content')
@php
    $assignmentData = $assignment->quiz_data ?? [];
    $selectedCourseId = old('course_id', $assignment->section?->course_id);
    $selectedSectionId = old('section_id', $assignment->course_section_id);
@endphp
<div class="max-w-3xl" x-data="{ courseId: '{{ $selectedCourseId }}', sections: @js($courses->mapWithKeys(fn($c) => [$c->id => $c->sections->map(fn($s) => ['id' => $s->id, 'title' => $s->title])])) }">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.assignments.update', $assignment) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')
            <x-form-input label="Assignment Title" name="title" :value="old('title', $assignment->title)" required />
            <x-form-input label="Course" name="course_id" type="select" required x-model="courseId">
                <option value="">Select course</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" @selected((string) $selectedCourseId === (string) $course->id)>{{ $course->title }}</option>
                @endforeach
            </x-form-input>
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700">Lesson / Section</label>
                <select name="section_id" required class="panel-input">
                    <option value="">Select section</option>
                    <template x-for="section in sections[courseId] || []" :key="section.id">
                        <option :value="section.id" x-text="section.title" :selected="section.id == '{{ $selectedSectionId }}'"></option>
                    </template>
                </select>
            </div>
            <x-form-input label="Description" name="description" type="textarea" :value="old('description', $assignment->content)" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-input label="Due Date" name="due_date" type="date" :value="old('due_date', $assignmentData['due_date'] ?? '')" />
                <x-form-input label="Marks" name="marks" type="number" step="0.01" :value="old('marks', $assignmentData['marks'] ?? '')" />
                <x-form-input label="Status" name="status" type="select" required>
                    <option value="draft" @selected(old('status', $assignmentData['status'] ?? 'published') === 'draft')>Draft</option>
                    <option value="published" @selected(old('status', $assignmentData['status'] ?? 'published') === 'published')>Published</option>
                </x-form-input>
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700">Upload File</label>
                @if($assignment->file_path)
                    <p class="text-sm text-slate-500 mb-2">Current file: {{ basename($assignment->file_path) }}</p>
                @endif
                <input type="file" name="file_path" class="w-full text-sm text-slate-500">
            </div>
            <div class="flex justify-between pt-4 border-t">
                <a href="{{ route('admin.assignments.index') }}" class="text-sm text-slate-500">Cancel</a>
                <button type="submit" class="panel-btn-primary">Save Assignment</button>
            </div>
        </form>
    </div>
</div>
@endsection
