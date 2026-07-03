@extends('layouts.app')

@section('title', 'Create Quiz')
@section('page-title', 'Create Quiz')
@section('breadcrumb', 'Quizzes / Create')

@section('content')
<div class="max-w-3xl" x-data="{ courseId: '{{ old('course_id') }}', sections: @js($courses->mapWithKeys(fn($c) => [$c->id => $c->sections->map(fn($s) => ['id' => $s->id, 'title' => $s->title])])) }">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.quizzes.store') }}" class="space-y-5">
            @csrf
            <x-form-input label="Quiz Title" name="title" required />
            <x-form-input label="Course" name="course_id" type="select" required x-model="courseId">
                <option value="">Select course</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                @endforeach
            </x-form-input>
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700">Lesson / Section</label>
                <select name="section_id" required class="panel-input">
                    <option value="">Select section</option>
                    <template x-for="section in sections[courseId] || []" :key="section.id">
                        <option :value="section.id" x-text="section.title" :selected="section.id == '{{ old('section_id') }}'"></option>
                    </template>
                </select>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-form-input label="Total Marks" name="total_marks" type="number" step="0.01" />
                <x-form-input label="Passing Marks" name="passing_marks" type="number" step="0.01" />
                <x-form-input label="Time Limit (min)" name="time_limit" type="number" />
            </div>
            <x-form-input label="Questions (JSON or notes)" name="questions[]" type="textarea" placeholder="Use curriculum builder for detailed question options, or add quiz via course edit." />
            <div class="flex justify-between pt-4 border-t">
                <a href="{{ route('admin.quizzes.index') }}" class="text-sm text-slate-500">Cancel</a>
                <button type="submit" class="panel-btn-primary">Create Quiz</button>
            </div>
        </form>
    </div>
</div>
@endsection
