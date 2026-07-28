@extends('layouts.app')

@section('title', 'Create Automation')
@section('page-title', 'New Automation')
@section('breadcrumb', 'Marketing / Automations / Create')

@section('content')
<div class="glass-card rounded-2xl p-6 max-w-3xl">
    <form method="POST" action="{{ route('admin.automations.store') }}" class="space-y-4">
        @csrf
        <x-form-input label="Name" name="name" required />
        <x-form-input label="Trigger" name="trigger_key" type="select" required>
            @foreach($triggers as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </x-form-input>
        <x-form-input label="Action" name="action_type" type="select" required>
            @foreach($actionTypes as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </x-form-input>
        <x-form-input label="Email subject" name="email_subject" placeholder="Welcome @{{name}}" />
        <x-form-input label="Message body" name="message" type="textarea" placeholder="Hi @{{name}}, ..." />
        <x-form-input label="Follow-up title" name="follow_up_title" />
        <x-form-input label="Follow-up due (hours)" name="due_hours" type="number" :value="24" />
        <x-form-input label="Segment" name="segment_id" type="select">
            <option value="">—</option>
            @foreach($segments as $segment)
                <option value="{{ $segment->id }}">{{ $segment->title }}</option>
            @endforeach
        </x-form-input>
        <x-form-input label="Coupon code" name="coupon_code" />
        <label class="flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-600 bg-slate-800 text-emerald-500">
            <span class="text-sm text-slate-300">Active</span>
        </label>
        <button class="px-5 py-2.5 rounded-xl panel-btn-primary">Create</button>
    </form>
</div>
@endsection
