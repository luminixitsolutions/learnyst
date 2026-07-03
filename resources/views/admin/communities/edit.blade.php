@extends('layouts.app')

@section('title', 'Edit Community')
@section('page-title', 'Edit Community')
@section('breadcrumb', $community->name)

@section('content')
<div class="max-w-2xl">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.communities.update', $community) }}" class="space-y-5">
            @csrf
            @method('PUT')
            <x-form-input label="Community Name" name="name" :value="$community->name" required />
            <x-form-input label="Description" name="description" type="textarea" :value="$community->description" />
            <label class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $community->is_active)) class="rounded border-slate-600 bg-slate-800 text-brand-500">
                <span class="text-sm text-slate-300">Active</span>
            </label>
            <label class="flex items-center gap-3">
                <input type="hidden" name="requires_approval" value="0">
                <input type="checkbox" name="requires_approval" value="1" @checked(old('requires_approval', $community->requires_approval)) class="rounded border-slate-600 bg-slate-800 text-brand-500">
                <span class="text-sm text-slate-300">Require approval to join</span>
            </label>
            <div class="flex justify-between pt-4">
                <a href="{{ route('admin.communities.show', $community) }}" class="text-sm text-slate-500">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
