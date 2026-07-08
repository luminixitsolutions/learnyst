@extends('layouts.app')

@section('title', 'Platform Settings')
@section('page-title', 'Platform Settings')
@section('breadcrumb', 'Platform Admin / Settings')

@section('content')
<div class="space-y-6">
    <form method="POST" action="{{ route('platform.settings.update') }}" class="glass-card rounded-2xl p-6 space-y-4 max-w-2xl">
        @csrf
        @method('PUT')
        <x-form-input label="Platform Name" name="site_name" :value="old('site_name', $settings['site_name'])" required />
        <x-form-input label="Support Email" name="support_email" type="email" :value="old('support_email', $settings['support_email'])" required />
        <label class="flex items-center gap-3">
            <input type="hidden" name="maintenance_mode" value="0">
            <input type="checkbox" name="maintenance_mode" value="1" @checked(old('maintenance_mode', $settings['maintenance_mode']) === '1') class="rounded border-slate-300 text-indigo-600">
            <span class="text-sm text-slate-700">Maintenance Mode</span>
        </label>
        <button type="submit" class="panel-btn-primary">Save Settings</button>
    </form>
</div>
@endsection
