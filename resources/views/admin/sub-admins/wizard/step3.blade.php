@extends('layouts.app')

@section('title', 'Sub Admin Wizard — Courses')
@section('page-title', 'Create Sub Admin')
@section('breadcrumb', 'Wizard / Step 3 — Courses')

@section('content')
<div class="max-w-3xl mx-auto">
    @include('admin.sub-admins.wizard._progress', ['current' => 3])

    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.sub-admins.wizard.store', 3) }}" class="space-y-5">
            @csrf
            <p class="text-sm text-slate-500">Optionally restrict this sub-admin to specific courses. Leave unchecked for full access.</p>
            @php $selected = old('course_ids', $data['course_ids'] ?? []); @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-80 overflow-y-auto p-4 rounded-xl bg-slate-900/80 border border-slate-200">
                @forelse($courses as $course)
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="course_ids[]" value="{{ $course->id }}" @checked(in_array($course->id, $selected)) class="rounded border-slate-600 bg-slate-800 text-brand-500">
                    <span class="text-sm text-slate-300">{{ $course->title }}</span>
                </label>
                @empty
                <p class="text-sm text-slate-500 col-span-2">No courses available</p>
                @endforelse
            </div>
            @error('course_ids')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
            <div class="flex justify-between pt-4 border-t border-slate-200">
                <a href="{{ route('admin.sub-admins.wizard.step', 2) }}" class="text-sm text-slate-500 hover:text-white">← Back</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Next: Bundles →</button>
            </div>
        </form>
    </div>
</div>
@endsection
