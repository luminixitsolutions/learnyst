@extends('layouts.app')

@section('title', 'Create Batch')
@section('page-title', 'Create Batch')
@section('breadcrumb', 'Batches / New')

@section('content')
<div class="max-w-3xl">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.batches.store') }}" class="space-y-5">
            @csrf
            <x-form-input label="Batch Title" name="title" required />
            <x-form-input label="Course" name="course_id" type="select" required>
                <option value="">Select course</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" @selected(old('course_id') == $course->id)>{{ $course->title }}</option>
                @endforeach
            </x-form-input>
            <x-form-input label="Select Instructor" name="instructor_id" type="select">
                <option value="">None</option>
                @foreach($instructors as $instructor)
                    <option value="{{ $instructor->id }}" @selected(old('instructor_id') == $instructor->id)>{{ $instructor->name }}</option>
                @endforeach
            </x-form-input>
            <x-form-input label="Description" name="description" type="textarea" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-form-input label="Price (₹)" name="price" type="number" step="0.01" />
                <x-form-input label="Start Date" name="start_date" type="date" />
                <x-form-input label="End Date" name="end_date" type="date" />
                <x-form-input label="Quiz Type" name="quiz_type" type="select">
                    <option value="online" @selected(old('quiz_type', 'online') === 'online')>Online Quiz</option>
                    <option value="offline" @selected(old('quiz_type') === 'offline')>Offline Quiz</option>
                </x-form-input>
                <x-form-input label="Select Template" name="template" placeholder="Template name" />
                <x-form-input label="Status" name="status" type="select" required>
                    @foreach(['upcoming','active','completed','cancelled'] as $st)
                        <option value="{{ $st }}" @selected(old('status', 'upcoming') === $st)>{{ ucfirst($st) }}</option>
                    @endforeach
                </x-form-input>
            </div>
            <label class="flex items-center gap-3">
                <input type="hidden" name="is_free" value="0">
                <input type="checkbox" name="is_free" value="1" @checked(old('is_free')) class="rounded border-slate-300 text-indigo-600">
                <span class="text-sm text-slate-700">Make this a Free Batch</span>
            </label>
            <div class="flex justify-between pt-4">
                <a href="{{ route('admin.batches.index') }}" class="text-sm text-slate-500">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Create Batch</button>
            </div>
        </form>
    </div>
</div>
@endsection
