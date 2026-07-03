@extends('layouts.app')

@section('title', 'Edit Live Class')
@section('page-title', 'Edit Live Class')
@section('breadcrumb', 'Live Classes / Edit')

@section('content')
<div class="max-w-3xl">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.live-classes.update', $liveClass) }}" class="space-y-5">
            @csrf @method('PUT')
            <x-form-input label="Class Title" name="title" :value="old('title', $liveClass->title)" required />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-form-input label="Course" name="course_id" type="select">
                    <option value="">Select course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" @selected(old('course_id', $liveClass->course_id) == $course->id)>{{ $course->title }}</option>
                    @endforeach
                </x-form-input>
                <x-form-input label="Batch" name="batch_id" type="select">
                    <option value="">Select batch</option>
                    @foreach($batches as $batch)
                        <option value="{{ $batch->id }}" @selected(old('batch_id', $liveClass->batch_id) == $batch->id)>{{ $batch->title }}</option>
                    @endforeach
                </x-form-input>
                <x-form-input label="Instructor" name="instructor_id" type="select">
                    <option value="">Select instructor</option>
                    @foreach($instructors as $instructor)
                        <option value="{{ $instructor->id }}" @selected(old('instructor_id', $liveClass->instructor_id) == $instructor->id)>{{ $instructor->name }}</option>
                    @endforeach
                </x-form-input>
                <x-form-input label="Platform" name="platform" type="select" required>
                    @foreach(['zoom','google_meet','youtube','other'] as $val)
                        <option value="{{ $val }}" @selected(old('platform', $liveClass->platform ?? 'zoom') === $val)>{{ str_replace('_', ' ', ucfirst($val)) }}</option>
                    @endforeach
                </x-form-input>
                <x-form-input label="Date" name="starts_at" type="date" required :value="old('starts_at', $liveClass->starts_at?->format('Y-m-d'))" />
                <x-form-input label="Start Time" name="start_time" type="time" required :value="old('start_time', $liveClass->starts_at?->format('H:i'))" />
                <x-form-input label="End Time" name="end_time" type="time" :value="old('end_time', $liveClass->ends_at?->format('H:i'))" />
                <x-form-input label="Status" name="status" type="select" required>
                    @foreach(['scheduled','live','completed','cancelled'] as $st)
                        <option value="{{ $st }}" @selected(old('status', $liveClass->status ?? 'scheduled') === $st)>{{ ucfirst($st) }}</option>
                    @endforeach
                </x-form-input>
            </div>
            <x-form-input label="Meeting Link" name="meeting_url" :value="old('meeting_url', $liveClass->meeting_url)" />
            <x-form-input label="Description" name="description" type="textarea" :value="old('description', $liveClass->description)" />
            <div class="flex justify-between pt-4 border-t">
                <a href="{{ route('admin.live-classes.index') }}" class="text-sm text-slate-500">Cancel</a>
                <button type="submit" class="panel-btn-primary">Update Live Class</button>
            </div>
        </form>
    </div>
</div>
@endsection
