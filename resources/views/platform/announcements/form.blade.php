@extends('layouts.app')

@section('title', $mode === 'create' ? 'New Announcement' : 'Edit Announcement')
@section('page-title', $mode === 'create' ? 'New Announcement' : 'Edit Announcement')
@section('breadcrumb', 'Platform Admin / Support / Announcements')

@section('content')
<div class="max-w-3xl space-y-6">
    <a href="{{ route('platform.announcements.index') }}" class="text-sm text-slate-500 hover:text-slate-800">← All announcements</a>

    <form method="POST"
          action="{{ $mode === 'create' ? route('platform.announcements.store') : route('platform.announcements.update', $announcement) }}"
          class="glass-card rounded-2xl p-6 space-y-4"
          x-data="{ audience: '{{ old('audience', $announcement->audience) }}' }">
        @csrf
        @if($mode === 'edit') @method('PUT') @endif

        <x-form-input label="Title" name="title" :value="old('title', $announcement->title)" required />

        <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-slate-700">Body <span class="text-red-500">*</span></label>
            <textarea name="body" rows="8" required class="panel-input w-full">{{ old('body', $announcement->body) }}</textarea>
            @error('body')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-slate-700">Audience</label>
                <select name="audience" x-model="audience" class="panel-input w-full">
                    <option value="all_institutes">All institutes</option>
                    <option value="institute_admins">Institute admins</option>
                    <option value="specific">Specific institute</option>
                </select>
            </div>
            <div class="space-y-1.5" x-show="audience === 'specific'" x-cloak>
                <label class="block text-sm font-semibold text-slate-700">Institute</label>
                <select name="company_id" class="panel-input w-full">
                    <option value="">Select…</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" @selected((string) old('company_id', $announcement->company_id) === (string) $company->id)>{{ $company->name }}</option>
                    @endforeach
                </select>
                @error('company_id')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-slate-700">Status</label>
                <select name="status" class="panel-input w-full">
                    @foreach(['draft','scheduled','published','archived'] as $st)
                        <option value="{{ $st }}" @selected(old('status', $announcement->status) === $st)>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-slate-700">Starts at</label>
                <input type="datetime-local" name="starts_at" class="panel-input w-full"
                       value="{{ old('starts_at', optional($announcement->starts_at)->format('Y-m-d\TH:i')) }}">
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-slate-700">Ends at</label>
                <input type="datetime-local" name="ends_at" class="panel-input w-full"
                       value="{{ old('ends_at', optional($announcement->ends_at)->format('Y-m-d\TH:i')) }}">
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-2">
            <a href="{{ route('platform.announcements.index') }}" class="panel-btn-secondary text-sm">Cancel</a>
            <button class="panel-btn-primary text-sm">{{ $mode === 'create' ? 'Create' : 'Save' }}</button>
        </div>
    </form>
</div>
@endsection
