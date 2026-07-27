@extends('layouts.app')

@section('title', 'Edit Quiz')
@section('page-title', 'Edit Quiz')
@section('breadcrumb', 'Quizzes / Edit')

@section('content')
@php
    $quizData = $quiz->quiz_data ?? [];
    $selectedCourseId = old('course_id', $quiz->section?->course_id);
    $selectedSectionId = old('section_id', $quiz->course_section_id);
@endphp
<div class="max-w-3xl" x-data="{ courseId: '{{ $selectedCourseId }}', sections: @js($courses->mapWithKeys(fn($c) => [$c->id => $c->sections->map(fn($s) => ['id' => $s->id, 'title' => $s->title])])) }">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.quizzes.update', $quiz) }}" class="space-y-5">
            @csrf
            @method('PUT')
            <x-form-input label="Quiz Title" name="title" :value="old('title', $quiz->title)" required />
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-form-input label="Total Marks" name="total_marks" type="number" step="0.01" :value="old('total_marks', $quizData['total_marks'] ?? '')" />
                <x-form-input label="Passing Marks" name="passing_marks" type="number" step="0.01" :value="old('passing_marks', $quizData['passing_marks'] ?? '')" />
                <x-form-input label="Time Limit (min)" name="time_limit" type="number" :value="old('time_limit', $quizData['time_limit'] ?? '')" />
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                <p class="font-semibold text-slate-800 mb-1">{{ count($quizData['questions'] ?? []) }} question(s) saved</p>
                <p>Use the course builder lesson editor for detailed question management, or update marks and section here.</p>
                <a href="{{ route('admin.lessons.edit', $quiz) }}" class="inline-flex mt-3 text-sm font-semibold text-indigo-600 hover:text-indigo-800">Open lesson editor →</a>
            </div>
            <div class="flex justify-between pt-4 border-t">
                <a href="{{ route('admin.quizzes.index') }}" class="text-sm text-slate-500">Cancel</a>
                <button type="submit" class="panel-btn-primary">Save Quiz</button>
            </div>
        </form>
    </div>
</div>
@endsection
