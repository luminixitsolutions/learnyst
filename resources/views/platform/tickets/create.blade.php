@extends('layouts.app')

@section('title', 'New Ticket')
@section('page-title', 'New Support Ticket')
@section('breadcrumb', 'Platform Admin / Support / Tickets')

@section('content')
<div class="max-w-2xl space-y-6">
    <a href="{{ route('platform.tickets.index') }}" class="text-sm text-slate-500 hover:text-slate-800">← Inbox</a>

    <form method="POST" action="{{ route('platform.tickets.store') }}" class="glass-card rounded-2xl p-6 space-y-4">
        @csrf
        <x-form-input label="Subject" name="subject" :value="old('subject')" required />

        <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-slate-700">Institute <span class="text-red-500">*</span></label>
            <select name="company_id" required class="panel-input w-full">
                <option value="">Select institute…</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}" @selected((string) old('company_id') === (string) $company->id)>{{ $company->name }}</option>
                @endforeach
            </select>
            @error('company_id')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-slate-700">Priority</label>
            <select name="priority" class="panel-input w-full">
                @foreach(['low','normal','high','urgent'] as $pr)
                    <option value="{{ $pr }}" @selected(old('priority', 'normal') === $pr)>{{ ucfirst($pr) }}</option>
                @endforeach
            </select>
        </div>

        <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-slate-700">Message <span class="text-red-500">*</span></label>
            <textarea name="body" rows="6" required class="panel-input w-full">{{ old('body') }}</textarea>
            @error('body')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <p class="text-xs text-slate-500">Requester defaults to the institute owner.</p>

        <div class="flex justify-end gap-2">
            <a href="{{ route('platform.tickets.index') }}" class="panel-btn-secondary text-sm">Cancel</a>
            <button class="panel-btn-primary text-sm">Create ticket</button>
        </div>
    </form>
</div>
@endsection
