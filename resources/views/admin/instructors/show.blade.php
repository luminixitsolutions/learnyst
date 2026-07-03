@extends('layouts.app')

@section('title', $instructor->name)
@section('page-title', $instructor->name)
@section('breadcrumb', 'Instructors / Profile')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-slate-800 flex items-center justify-center text-xl font-bold text-indigo-600">{{ strtoupper(substr($instructor->name, 0, 1)) }}</div>
            <div>
                <p class="text-slate-500 text-sm">{{ $instructor->email }}</p>
                @if($instructor->phone)<p class="text-slate-500 text-sm">{{ $instructor->phone }}</p>@endif
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.instructors.edit', $instructor) }}" class="px-4 py-2 rounded-xl bg-brand-600 text-white text-sm">Edit</a>
            <a href="{{ route('admin.instructors.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-300 text-sm">Back</a>
        </div>
    </div>

    @if($instructor->bio)
    <div class="glass-card rounded-2xl p-6"><p class="text-sm text-slate-500">{{ $instructor->bio }}</p></div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Assign Course</h3>
            <form method="POST" action="{{ route('admin.instructors.courses.assign', $instructor) }}" class="flex flex-wrap items-end gap-4 mb-6">
                @csrf
                <div class="flex-1 min-w-[200px]">
                    <x-form-input label="Course" name="course_id" type="select" required>
                        <option value="">Select course</option>
                        @foreach($availableCourses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </x-form-input>
                </div>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 text-white text-sm hover:bg-brand-500">Assign</button>
            </form>
            <h4 class="text-sm font-medium text-slate-500 mb-3">Assigned Courses ({{ $instructor->courses->count() }})</h4>
            @forelse($instructor->courses as $course)
                <div class="flex items-center justify-between py-2 border-b border-slate-200 last:border-0">
                    <a href="{{ route('admin.courses.show', $course) }}" class="text-sm text-white hover:text-indigo-600">{{ $course->title }}</a>
                    <form method="POST" action="{{ route('admin.instructors.courses.remove', [$instructor, $course]) }}">@csrf @method('DELETE')
                        <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-xs text-red-500 hover:text-red-700">Remove</button>
                    </form>
                </div>
            @empty
                <p class="text-sm text-slate-500">No courses assigned</p>
            @endforelse
        </div>

        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Assign Batch</h3>
            <form method="POST" action="{{ route('admin.instructors.batches.assign', $instructor) }}" class="flex flex-wrap items-end gap-4 mb-6">
                @csrf
                <div class="flex-1 min-w-[200px]">
                    <x-form-input label="Batch" name="batch_id" type="select" required>
                        <option value="">Select batch</option>
                        @foreach($availableBatches as $batch)
                            <option value="{{ $batch->id }}">{{ $batch->title }} @if($batch->course) — {{ $batch->course->title }} @endif</option>
                        @endforeach
                    </x-form-input>
                </div>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 text-white text-sm hover:bg-brand-500">Assign</button>
            </form>
            <h4 class="text-sm font-medium text-slate-500 mb-3">Assigned Batches ({{ $assignedBatches->count() }})</h4>
            @forelse($assignedBatches as $batch)
                <div class="py-2 border-b border-slate-200 last:border-0">
                    <a href="{{ route('admin.batches.show', $batch) }}" class="text-sm text-white hover:text-indigo-600">{{ $batch->title }}</a>
                    <x-badge type="info" class="ml-2">{{ ucfirst($batch->status) }}</x-badge>
                    @if($batch->course)<p class="text-xs text-slate-500 mt-0.5">{{ $batch->course->title }}</p>@endif
                </div>
            @empty
                <p class="text-sm text-slate-500">No batches assigned</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
