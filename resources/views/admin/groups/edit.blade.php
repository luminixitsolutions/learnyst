@extends('layouts.app')

@section('title', 'Edit Group')
@section('page-title', 'Edit Group')
@section('breadcrumb', $group->name)

@section('content')
<div class="max-w-2xl">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.groups.update', $group) }}" class="space-y-5">
            @csrf
            @method('PUT')
            <x-form-input label="Group Name" name="name" :value="old('name', $group->name)" required />
            <x-form-input label="Description" name="description" type="textarea" :value="old('description', $group->description)" />
            <label class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $group->is_active)) class="rounded border-slate-600 bg-slate-800 text-brand-500">
                <span class="text-sm text-slate-300">Active</span>
            </label>
            <div class="flex justify-between pt-4 border-t border-slate-200">
                <a href="{{ route('admin.groups.show', $group) }}" class="text-sm text-slate-500 hover:text-white">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
