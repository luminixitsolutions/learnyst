@extends('layouts.app')

@section('title', $group->name)
@section('page-title', $group->name)
@section('breadcrumb', 'Groups / Details')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <x-badge :type="$group->is_active ? 'success' : 'danger'">{{ $group->is_active ? 'Active' : 'Inactive' }}</x-badge>
            @if($group->description)<p class="text-sm text-slate-500 mt-2">{{ $group->description }}</p>@endif
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.groups.edit', $group) }}" class="px-4 py-2 rounded-xl bg-brand-600 text-white text-sm hover:bg-brand-500">Edit</a>
            <a href="{{ route('admin.groups.index') }}" class="panel-btn-secondary">Back</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Add Learner</h3>
            <form method="POST" action="{{ route('admin.groups.learners.add', $group) }}" class="space-y-4">
                @csrf
                <x-form-input label="Learner" name="user_id" type="select" required>
                    <option value="">Select learner</option>
                    @foreach($availableLearners as $learner)
                        <option value="{{ $learner->id }}">{{ $learner->name }} ({{ $learner->email }})</option>
                    @endforeach
                </x-form-input>
                <button type="submit" class="w-full py-2 rounded-xl bg-brand-600 text-white text-sm hover:bg-brand-500">Add to Group</button>
            </form>
            <div class="mt-6">
                <p class="text-xs text-slate-500 mb-3">{{ $group->learners->count() }} learners</p>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @forelse($group->learners as $learner)
                    <div class="flex items-center justify-between py-2 border-b border-slate-200 last:border-0">
                        <div>
                            <a href="{{ route('admin.learners.show', $learner) }}" class="text-sm text-white hover:text-indigo-600">{{ $learner->name }}</a>
                            <p class="text-xs text-slate-500">{{ $learner->email }}</p>
                        </div>
                        <form method="POST" action="{{ route('admin.groups.learners.remove', [$group, $learner]) }}">@csrf @method('DELETE')
                            <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-xs text-red-500 hover:text-red-700">Remove</button>
                        </form>
                    </div>
                    @empty
                    <p class="text-sm text-slate-500">No learners in this group</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Assign Course</h3>
            <form method="POST" action="{{ route('admin.groups.courses.assign', $group) }}" class="space-y-4">
                @csrf
                <x-form-input label="Course" name="course_id" type="select" required>
                    <option value="">Select course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                    @endforeach
                </x-form-input>
                <button type="submit" class="w-full py-2 rounded-xl bg-brand-600 text-white text-sm hover:bg-brand-500">Assign Course</button>
            </form>
            <div class="mt-6">
                <p class="text-xs text-slate-500 mb-3">{{ $group->courses->count() }} courses</p>
                <div class="space-y-2">
                    @forelse($group->courses as $course)
                    <div class="flex items-center justify-between py-2 border-b border-slate-200 last:border-0">
                        <a href="{{ route('admin.courses.show', $course) }}" class="text-sm text-white hover:text-indigo-600">{{ $course->title }}</a>
                        <form method="POST" action="{{ route('admin.groups.courses.remove', [$group, $course]) }}">@csrf @method('DELETE')
                            <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-xs text-red-500 hover:text-red-700">Remove</button>
                        </form>
                    </div>
                    @empty
                    <p class="text-sm text-slate-500">No courses assigned</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
