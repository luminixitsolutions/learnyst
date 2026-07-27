@extends('layouts.app')

@section('title', 'Notification Center')
@section('page-title', 'Notification Center')
@section('breadcrumb', 'Settings / Notifications')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.notifications.update') }}" class="glass-card rounded-2xl p-6 space-y-5">
        @csrf
        @method('PUT')
        <h3 class="font-bold text-slate-800">Channels</h3>
        @foreach([
            'email_enabled' => 'Email notifications',
            'sms_enabled' => 'SMS notifications',
            'whatsapp_enabled' => 'WhatsApp notifications',
            'push_enabled' => 'Push notifications',
        ] as $key => $label)
            <label class="flex items-center gap-3">
                <input type="hidden" name="{{ $key }}" value="0">
                <input type="checkbox" name="{{ $key }}" value="1" @checked($channels[$key]) class="rounded border-slate-300 text-indigo-600">
                <span class="text-sm text-slate-700">{{ $label }}</span>
            </label>
        @endforeach

        <hr class="border-slate-200">
        <h3 class="font-bold text-slate-800">Certificate expiry</h3>
        <label class="flex items-center gap-3">
            <input type="hidden" name="certificate_expiry_email" value="0">
            <input type="checkbox" name="certificate_expiry_email" value="1" @checked($channels['certificate_expiry_email']) class="rounded border-slate-300 text-indigo-600">
            <span class="text-sm">Send email reminders at 90 / 60 / 30 days</span>
        </label>
        <label class="flex items-center gap-3">
            <input type="hidden" name="certificate_expiry_in_app" value="0">
            <input type="checkbox" name="certificate_expiry_in_app" value="1" @checked($channels['certificate_expiry_in_app']) class="rounded border-slate-300 text-indigo-600">
            <span class="text-sm">Create in-app notifications</span>
        </label>

        <button type="submit" class="panel-btn-primary">Save Notification Settings</button>
    </form>
</div>
@endsection
