@extends('layouts.app')

@section('title', 'Edit Resource')
@section('page-title', 'Edit Resource')
@section('breadcrumb', $resource->title)

@section('content')
<div class="max-w-3xl">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.resources.update', $resource) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')
            <x-form-input label="Title" name="title" :value="$resource->title" required />
            <x-form-input label="Description" name="description" type="textarea" :value="$resource->description" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-form-input label="Resource Type" name="resource_type" type="select" required>
                    @foreach(['pdf','video','link','file'] as $type)
                        <option value="{{ $type }}" @selected(old('resource_type', $resource->resource_type) === $type)>{{ strtoupper($type) }}</option>
                    @endforeach
                </x-form-input>
                <x-form-input label="Category" name="category_id" type="select">
                    <option value="">None</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('category_id', $resource->category_id) == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </x-form-input>
                <x-form-input label="External URL" name="external_url" :value="$resource->external_url" />
                <x-form-input label="Status" name="status" type="select" required>
                    @foreach(['draft','published','unpublished'] as $st)
                        <option value="{{ $st }}" @selected(old('status', $resource->status) === $st)>{{ ucfirst($st) }}</option>
                    @endforeach
                </x-form-input>
            </div>
            @if($resource->file_path)
                <p class="text-xs text-slate-500">Current file: {{ basename($resource->file_path) }}</p>
            @endif
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">Replace File</label>
                <input type="file" name="file_path" class="text-sm text-slate-500">
            </div>
            <div class="flex justify-between pt-4">
                <a href="{{ route('admin.resources.index') }}" class="text-sm text-slate-500">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
