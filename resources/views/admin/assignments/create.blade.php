@extends('layouts.app')

@section('title', 'Create Assignment')
@section('page-title', 'Create Assignment')
@section('breadcrumb', 'Assignments / Create')

@section('content')
<div class="max-w-3xl" x-data="{ courseId: '{{ old('course_id') }}', sections: @js($courses->mapWithKeys(fn($c) => [$c->id => $c->sections->map(fn($s) => ['id' => $s->id, 'title' => $s->title])])) }">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.assignments.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <x-form-input label="Assignment Title" name="title" required />
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
                        <option :value="section.id" x-text="section.title"></option>
                    </template>
                </select>
            </div>
            <x-form-input label="Description" name="description" type="textarea" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-input label="Due Date" name="due_date" type="date" />
                <x-form-input label="Marks" name="marks" type="number" step="0.01" />
                <x-form-input label="Status" name="status" type="select" required>
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </x-form-input>
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700">Upload File</label>
                <input type="file" name="file_path" class="w-full text-sm text-slate-500">
            </div>
            <div class="flex justify-between pt-4 border-t">
                <a href="{{ route('admin.assignments.index') }}" class="text-sm text-slate-500">Cancel</a>
                <button type="submit" class="panel-btn-primary">Create Assignment</button>
            </div>
        </form>
    </div>
</div>
@endsection
