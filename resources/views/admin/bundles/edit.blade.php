@extends('layouts.app')

@section('title', 'Edit Bundle')
@section('page-title', 'Edit Bundle')
@section('breadcrumb', $bundle->title)

@section('content')
<div class="max-w-3xl">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.bundles.update', $bundle) }}" class="space-y-6">
            @csrf
            @method('PUT')
            <x-form-input label="Title" name="title" :value="old('title', $bundle->title)" required />
            <x-form-input label="Description" name="description" type="textarea" :value="old('description', $bundle->description)" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-form-input label="Price (₹)" name="price" type="number" step="0.01" :value="old('price', $bundle->price)" />
                <x-form-input label="Status" name="status" type="select" required>
                    @foreach(['draft', 'published', 'unpublished'] as $st)
                        <option value="{{ $st }}" @selected(old('status', $bundle->status) === $st)>{{ ucfirst($st) }}</option>
                    @endforeach
                </x-form-input>
            </div>
            <div class="space-y-2">
                <label class="block text-sm font-medium text-slate-300">Courses <span class="text-red-400">*</span></label>
                @php $selectedCourses = old('course_ids', $bundle->courses->pluck('id')->toArray()); @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-64 overflow-y-auto p-4 rounded-xl bg-slate-900/80 border border-slate-200">
                    @foreach($courses as $course)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="course_ids[]" value="{{ $course->id }}" @checked(in_array($course->id, $selectedCourses)) class="rounded border-slate-600 bg-slate-800 text-brand-500">
                        <span class="text-sm text-slate-300">{{ $course->title }}</span>
                    </label>
                    @endforeach
                </div>
                @error('course_ids')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div class="flex justify-between pt-4 border-t border-slate-200">
                <a href="{{ route('admin.bundles.show', $bundle) }}" class="text-sm text-slate-500 hover:text-white">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
