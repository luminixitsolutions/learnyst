@extends('layouts.app')

@section('title', 'Live Class Lesson')
@section('page-title', 'Live Class Lesson')
@section('breadcrumb', $lesson->title)

@section('content')
@php
    $liveClass = $lesson->liveClass;
    $selectedType = old('live_class_type', $liveClass?->live_class_type ?? 'super_live');
    $selectedLayout = old('recording_layout_mode', $liveClass?->recording_layout_mode ?? 'host_screen_and_camera');
@endphp
<div x-data="{
    showSettings: false,
    showLiveConfig: true,
    liveClassType: '{{ $selectedType }}',
    isSuperLive() { return this.liveClassType === 'super_live'; },
    isMeeting() { return this.liveClassType === 'learnyst_meeting'; },
    isWebinar() { return this.liveClassType === 'learnyst_webinar'; },
    isEmbed() { return this.liveClassType === 'embed_live_class'; },
    showScheduleFields() { return this.isSuperLive() || this.isMeeting() || this.isWebinar(); },
    showRecordingOptions() { return !this.isEmbed(); },
}">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="glass-card rounded-2xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.courses.builder', $course) }}" class="p-2 rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </a>
                        <h2 class="text-xl font-bold text-slate-800">Live Class Lesson</h2>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" @click="showLiveConfig = !showLiveConfig" class="px-3 py-2 rounded-xl border border-slate-200 text-sm">Configure Live Class</button>
                        <button type="button" @click="showSettings = !showSettings" class="px-3 py-2 rounded-xl border border-slate-200 text-sm">Settings</button>
                        <form method="POST" action="{{ route('admin.lessons.remove', $lesson) }}" class="inline">@csrf @method('DELETE')
                            <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="px-3 py-2 rounded-xl border border-red-200 text-sm text-red-600">Remove</button>
                        </form>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.lessons.update', $lesson) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <x-form-input label="Lesson Title" name="title" :value="$lesson->title" required />

                    <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-12 text-center">
                        <div class="w-16 h-16 mx-auto rounded-full bg-indigo-100 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </div>
                        <p class="text-slate-600 font-medium">Live Class Placeholder</p>
                        @if($liveClass?->starts_at)
                        <p class="text-sm text-slate-500 mt-2">Scheduled: {{ $liveClass->starts_at->format('M d, Y h:i A') }}</p>
                        <p class="text-sm text-slate-500">Duration: {{ $liveClass->duration_hours }}h {{ $liveClass->duration_minutes }}m</p>
                        <p class="text-sm text-slate-500">Type: {{ \App\Models\LiveClass::TYPES[$liveClass->live_class_type] ?? $liveClass->live_class_type }}</p>
                        @elseif($liveClass?->embed_url)
                        <p class="text-sm text-slate-500 mt-2">Embed URL configured</p>
                        @else
                        <p class="text-sm text-slate-400 mt-2">Configure live class settings to schedule</p>
                        @endif
                    </div>

                    <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Save</button>
                </form>
            </div>

            @include('admin.lessons.partials.attachments-sidebar', ['lesson' => $lesson])
        </div>

        {{-- Configure Live Class side panel --}}
        <div x-show="showLiveConfig" class="glass-card rounded-2xl p-6 h-fit">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Configure Live Class</h3>
            <form method="POST" action="{{ route('admin.lessons.live-class.update', $lesson) }}" class="space-y-4">
                @csrf
                @method('PUT')

                {{-- Live Class Type --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-slate-700">Live Class Type</label>
                    <select name="live_class_type" x-model="liveClassType" required class="panel-select w-full">
                        @foreach(\App\Models\LiveClass::TYPES as $value => $label)
                            <option value="{{ $value }}" @selected($selectedType === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Super Live / Webinar Capacity --}}
                <div x-show="isSuperLive()" x-cloak>
                    <x-form-input label="Super Live Capacity" name="super_live_capacity" type="number" :value="$liveClass?->super_live_capacity" placeholder="e.g. 500" />
                </div>
                <div x-show="isWebinar()" x-cloak>
                    <x-form-input label="Webinar Capacity" name="super_live_capacity" type="number" :value="$liveClass?->super_live_capacity" placeholder="e.g. 1000" />
                </div>

                {{-- Embed URL --}}
                <div x-show="isEmbed()" x-cloak>
                    <x-form-input label="Embed Live Class URL" name="embed_url" :value="$liveClass?->embed_url" placeholder="https://..." />
                </div>

                {{-- Schedule fields --}}
                <div x-show="showScheduleFields()" x-cloak class="space-y-4">
                    <x-form-input label="Start Date" name="starts_at" type="date" :value="$liveClass?->starts_at?->format('Y-m-d')" />
                    <x-form-input label="Start Time" name="start_time" type="time" :value="$liveClass?->starts_at?->format('H:i')" />
                    <div class="grid grid-cols-2 gap-3">
                        <x-form-input label="Hours" name="duration_hours" type="number" :value="$liveClass?->duration_hours ?? 0" />
                        <x-form-input label="Minutes" name="duration_minutes" type="number" :value="$liveClass?->duration_minutes ?? 0" />
                    </div>
                </div>

                {{-- Recording Layout Mode --}}
                <div x-show="showRecordingOptions()" x-cloak class="space-y-1.5">
                    <label class="block text-sm font-semibold text-slate-700">Recording Layout Mode</label>
                    <select name="recording_layout_mode" class="panel-select w-full">
                        @foreach(\App\Models\LiveClass::RECORDING_LAYOUTS as $value => $label)
                            <option value="{{ $value }}" @selected($selectedLayout === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Checkboxes --}}
                <div x-show="showRecordingOptions()" x-cloak class="space-y-2">
                    <label class="flex items-center gap-2"><input type="checkbox" name="new_recording" value="1" @checked($liveClass?->new_recording ?? true) class="rounded text-indigo-600"><span class="text-sm text-slate-700">New Recording</span></label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="enable_participant_list" value="1" @checked($liveClass?->enable_participant_list ?? true) class="rounded text-indigo-600"><span class="text-sm text-slate-700">Enable Participant List</span></label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="chat_box" value="1" @checked($liveClass?->chat_box ?? true) class="rounded text-indigo-600"><span class="text-sm text-slate-700">Chat Box</span></label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="enable_qa" value="1" @checked($liveClass?->enable_qa ?? true) class="rounded text-indigo-600"><span class="text-sm text-slate-700">Enable Q&A</span></label>
                    <label class="flex items-center gap-2" x-show="isMeeting() || isSuperLive()"><input type="checkbox" name="show_whiteboard" value="1" @checked($liveClass?->show_whiteboard ?? false) class="rounded text-indigo-600"><span class="text-sm text-slate-700">Show Whiteboard</span></label>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="submit" class="flex-1 px-4 py-2 rounded-xl panel-btn-primary text-sm">Save</button>
                    <button type="button" @click="showLiveConfig = false" class="px-4 py-2 rounded-xl border border-slate-200 text-sm">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    @include('admin.lessons.partials.settings-panel', ['lesson' => $lesson])
</div>
@endsection
