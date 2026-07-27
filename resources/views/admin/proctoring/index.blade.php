@extends('layouts.app')

@section('title', 'Exam Proctoring')
@section('page-title', 'Exam Proctoring Settings')
@section('breadcrumb', 'Teaching / Proctoring')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.proctoring.update') }}" class="glass-card rounded-2xl p-6 space-y-5">
        @csrf
        @method('PUT')
        <p class="text-sm text-slate-600">Default proctoring flags applied to mock tests and test series (incident review queue in Phase 5).</p>

        <label class="flex items-center gap-3">
            <input type="hidden" name="webcam_required" value="0">
            <input type="checkbox" name="webcam_required" value="1" @checked($settings['webcam_required']) class="rounded border-slate-300 text-indigo-600">
            <span class="text-sm text-slate-700">Require webcam for proctored exams</span>
        </label>
        <label class="flex items-center gap-3">
            <input type="hidden" name="tab_switch_detection" value="0">
            <input type="checkbox" name="tab_switch_detection" value="1" @checked($settings['tab_switch_detection']) class="rounded border-slate-300 text-indigo-600">
            <span class="text-sm text-slate-700">Detect tab switching</span>
        </label>
        <label class="flex items-center gap-3">
            <input type="hidden" name="lockdown_mode" value="0">
            <input type="checkbox" name="lockdown_mode" value="1" @checked($settings['lockdown_mode']) class="rounded border-slate-300 text-indigo-600">
            <span class="text-sm text-slate-700">Enable lockdown mode</span>
        </label>
        <x-form-input label="Incident retention (days)" name="incident_retention_days" type="number" :value="$settings['incident_retention_days']" required />

        <button type="submit" class="panel-btn-primary">Save Settings</button>
    </form>
</div>
@endsection
