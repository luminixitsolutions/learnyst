@extends('layouts.app')

@section('title', 'Edit Batch')
@section('page-title', 'Edit Batch')
@section('breadcrumb', $batch->title)

@section('content')
<div class="max-w-3xl">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.batches.update', $batch) }}" class="space-y-5">
            @csrf
            @method('PUT')
            <x-form-input label="Batch Title" name="title" :value="$batch->title" required />
            <x-form-input label="Course" name="course_id" type="select" required>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" @selected(old('course_id', $batch->course_id) == $course->id)>{{ $course->title }}</option>
                @endforeach
            </x-form-input>
            <x-form-input label="Instructor" name="instructor_id" type="select">
                <option value="">None</option>
                @foreach($instructors as $instructor)
                    <option value="{{ $instructor->id }}" @selected(old('instructor_id', $batch->instructor_id) == $instructor->id)>{{ $instructor->name }}</option>
                @endforeach
            </x-form-input>
            <x-form-input label="Description" name="description" type="textarea" :value="$batch->description" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-form-input label="Price (₹)" name="price" type="number" step="0.01" :value="$batch->price" />
                <x-form-input label="Start Date" name="start_date" type="date" :value="$batch->start_date?->format('Y-m-d')" />
                <x-form-input label="End Date" name="end_date" type="date" :value="$batch->end_date?->format('Y-m-d')" />
                <x-form-input label="Status" name="status" type="select" required>
                    @foreach(['upcoming','active','completed','cancelled'] as $st)
                        <option value="{{ $st }}" @selected(old('status', $batch->status) === $st)>{{ ucfirst($st) }}</option>
                    @endforeach
                </x-form-input>
            </div>
            <label class="flex items-center gap-3">
                <input type="hidden" name="is_online" value="0">
                <input type="checkbox" name="is_online" value="1" @checked(old('is_online', $batch->is_online)) class="rounded border-slate-600 bg-slate-800 text-brand-500">
                <span class="text-sm text-slate-300">Online batch</span>
            </label>
            <div class="flex justify-between pt-4">
                <a href="{{ route('admin.batches.show', $batch) }}" class="text-sm text-slate-500">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
