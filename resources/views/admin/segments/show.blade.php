@extends('layouts.app')

@section('title', $segment->title)
@section('page-title', $segment->title)
@section('breadcrumb', 'Segments / Details')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-start">
        <p class="text-sm text-slate-500">{{ $segment->description ?? 'No description' }}</p>
        <a href="{{ route('admin.segments.index') }}" class="text-sm text-slate-500 hover:text-white">Back</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Assign Learner</h3>
            <form method="POST" action="{{ route('admin.segments.learners.assign', $segment) }}" class="flex gap-3">
                @csrf
                <select name="user_id" required class="flex-1 px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm">
                    <option value="">Select learner</option>
                    @foreach($learners as $learner)
                        <option value="{{ $learner->id }}">{{ $learner->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 text-white text-sm">Add</button>
            </form>
            <div class="mt-4 space-y-2">
                @forelse($segment->users as $user)
                    <p class="text-sm text-slate-500">{{ $user->name }} · {{ $user->email }}</p>
                @empty
                    <p class="text-sm text-slate-500">No learners assigned</p>
                @endforelse
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Assign Course</h3>
            <form method="POST" action="{{ route('admin.segments.courses.assign', $segment) }}" class="flex gap-3">
                @csrf
                <select name="course_id" required class="flex-1 px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm">
                    <option value="">Select course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 text-white text-sm">Add</button>
            </form>
            <div class="mt-4 space-y-2">
                @forelse($segment->courses as $course)
                    <p class="text-sm text-slate-500">{{ $course->title }}</p>
                @empty
                    <p class="text-sm text-slate-500">No courses assigned</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Update Segment</h3>
        <form method="POST" action="{{ route('admin.segments.update', $segment) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf @method('PUT')
            <x-form-input label="Title" name="title" :value="$segment->title" required />
            <x-form-input label="Description" name="description" :value="$segment->description" />
            <label class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $segment->is_active ?? true)) class="rounded border-slate-600 bg-slate-800 text-brand-500">
                <span class="text-sm text-slate-300">Active</span>
            </label>
            <div class="md:col-span-2">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 text-white text-sm">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection
