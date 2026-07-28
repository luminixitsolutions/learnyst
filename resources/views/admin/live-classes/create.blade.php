@extends('layouts.app')

@section('title', 'Schedule Live Class')
@section('page-title', 'Schedule Live Class')
@section('breadcrumb', 'Live Classes / Create')

@section('content')
<div class="max-w-3xl">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.live-classes.store') }}" class="space-y-5">
            @csrf
            <x-form-input label="Class Title" name="title" required />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-form-input label="Course" name="course_id" type="select">
                    <option value="">Select course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" @selected(old('course_id') == $course->id)>{{ $course->title }}</option>
                    @endforeach
                </x-form-input>
                <x-form-input label="Batch" name="batch_id" type="select">
                    <option value="">Select batch</option>
                    @foreach($batches as $batch)
                        <option value="{{ $batch->id }}" @selected(old('batch_id') == $batch->id)>{{ $batch->title }}</option>
                    @endforeach
                </x-form-input>
                <x-form-input label="Instructor" name="instructor_id" type="select">
                    <option value="">Select instructor</option>
                    @foreach($instructors as $instructor)
                        <option value="{{ $instructor->id }}" @selected(old('instructor_id') == $instructor->id)>{{ $instructor->name }}</option>
                    @endforeach
                </x-form-input>
                <x-form-input label="Platform" name="platform" type="select" required>
                    @foreach(['zoom' => 'Zoom', 'google_meet' => 'Google Meet', 'youtube' => 'YouTube', 'microsoft_teams' => 'Microsoft Teams', 'other' => 'Other'] as $val => $label)
                        <option value="{{ $val }}" @selected(old('platform', 'zoom') === $val)>{{ $label }}</option>
                    @endforeach
                </x-form-input>
                <x-form-input label="Date" name="starts_at" type="date" required :value="old('starts_at')" />
                <x-form-input label="Start Time" name="start_time" type="time" required :value="old('start_time')" />
                <x-form-input label="End Time" name="end_time" type="time" :value="old('end_time')" />
                <x-form-input label="Status" name="status" type="select" required>
                    @foreach(['scheduled','live','completed','cancelled'] as $st)
                        <option value="{{ $st }}" @selected(old('status', 'scheduled') === $st)>{{ ucfirst($st) }}</option>
                    @endforeach
                </x-form-input>
            </div>
            <x-form-input label="Meeting Link" name="meeting_url" placeholder="https://" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-form-input label="Meeting ID (optional)" name="meeting_id" placeholder="Teams / Zoom meeting id" />
                <x-form-input label="Passcode (optional)" name="meeting_passcode" />
            </div>
            <x-form-input label="Recording URL (optional)" name="recording_url" placeholder="https://" />
            <x-form-input label="Description" name="description" type="textarea" />
            <div class="flex justify-between pt-4 border-t">
                <a href="{{ route('admin.live-classes.index') }}" class="text-sm text-slate-500">Cancel</a>
                <button type="submit" class="panel-btn-primary">Save Live Class</button>
            </div>
        </form>
    </div>
</div>
@endsection
