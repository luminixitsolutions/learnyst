@extends('layouts.app')

@section('title', 'Edit Live Class')
@section('page-title', 'Edit Live Class')
@section('breadcrumb', 'Live Classes / Edit')

@section('content')
<div class="max-w-3xl">
    <div class="glass-card rounded-2xl p-6" id="live-class-form-fields"
         data-ai-url="{{ route('admin.live-classes.ai-analyze') }}"
         data-csrf="{{ csrf_token() }}">
        <form method="POST" action="{{ route('admin.live-classes.update', $liveClass) }}" class="space-y-5">
            @csrf @method('PUT')

            <div class="rounded-xl border border-indigo-100 bg-indigo-50/60 p-4">
                <div class="flex flex-col md:flex-row md:items-end gap-3">
                    <div class="flex-1 space-y-1.5">
                        <label for="title" class="block text-sm font-semibold text-slate-700">Class Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title', $liveClass->title) }}" required
                               placeholder="e.g. Eloquent Relationships Deep Dive"
                               class="panel-input w-full">
                        @error('title')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="button" id="ai-analyze-btn"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shrink-0 disabled:opacity-60 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span id="ai-analyze-label">AI Fill Details</span>
                    </button>
                </div>
                <div class="mt-3 space-y-1.5">
                    <label for="ai_brief" class="block text-xs font-medium text-slate-600">Optional notes for AI (audience, duration, focus)</label>
                    <input type="text" id="ai_brief" class="panel-input w-full text-sm" placeholder="e.g. Intermediate batch, 60 mins, evening slot, doubt-heavy">
                </div>
                <p id="ai-analyze-status" class="mt-2 text-xs text-slate-500 hidden"></p>
                <div id="ai-agenda-preview" class="mt-3 hidden">
                    <p class="text-xs font-semibold text-slate-600 mb-1">Suggested agenda</p>
                    <ul id="ai-agenda-list" class="text-xs text-slate-600 list-disc pl-5 space-y-0.5"></ul>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-form-input label="Course" name="course_id" type="select">
                    <option value="">Select course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" @selected(old('course_id', $liveClass->course_id) == $course->id)>{{ $course->title }}</option>
                    @endforeach
                </x-form-input>
                <x-form-input label="Batch" name="batch_id" type="select">
                    <option value="">Select batch</option>
                    @foreach($batches as $batch)
                        <option value="{{ $batch->id }}" @selected(old('batch_id', $liveClass->batch_id) == $batch->id)>{{ $batch->title }}</option>
                    @endforeach
                </x-form-input>
                <x-form-input label="Instructor" name="instructor_id" type="select">
                    <option value="">Select instructor</option>
                    @foreach($instructors as $instructor)
                        <option value="{{ $instructor->id }}" @selected(old('instructor_id', $liveClass->instructor_id) == $instructor->id)>{{ $instructor->name }}</option>
                    @endforeach
                </x-form-input>
                <x-form-input label="Platform" name="platform" type="select" required>
                    @foreach(['zoom' => 'Zoom', 'google_meet' => 'Google Meet', 'youtube' => 'YouTube', 'microsoft_teams' => 'Microsoft Teams', 'other' => 'Other'] as $val => $label)
                        <option value="{{ $val }}" @selected(old('platform', $liveClass->platform ?? 'zoom') === $val)>{{ $label }}</option>
                    @endforeach
                </x-form-input>
                <x-form-input label="Date" name="starts_at" type="date" required :value="old('starts_at', $liveClass->starts_at?->format('Y-m-d'))" />
                <x-form-input label="Start Time" name="start_time" type="time" required :value="old('start_time', $liveClass->starts_at?->format('H:i'))" />
                <x-form-input label="End Time" name="end_time" type="time" :value="old('end_time', $liveClass->ends_at?->format('H:i'))" />
                <x-form-input label="Status" name="status" type="select" required>
                    @foreach(['scheduled','live','completed','cancelled'] as $st)
                        <option value="{{ $st }}" @selected(old('status', $liveClass->status ?? 'scheduled') === $st)>{{ ucfirst($st) }}</option>
                    @endforeach
                </x-form-input>
            </div>
            <x-form-input label="Meeting Link" name="meeting_url" :value="old('meeting_url', $liveClass->meeting_url)" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-form-input label="Meeting ID (optional)" name="meeting_id" :value="old('meeting_id', $liveClass->meeting_id)" />
                <x-form-input label="Passcode (optional)" name="meeting_passcode" :value="old('meeting_passcode', $liveClass->meeting_passcode)" />
            </div>
            <x-form-input label="Recording URL (optional)" name="recording_url" :value="old('recording_url', $liveClass->recording_url)" />
            <div class="space-y-1.5">
                <label for="description" class="block text-sm font-semibold text-slate-700">Description</label>
                <textarea name="description" id="description" rows="6" class="panel-input">{{ old('description', $liveClass->description) }}</textarea>
                @error('description')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex justify-between pt-4 border-t">
                <a href="{{ route('admin.live-classes.index') }}" class="text-sm text-slate-500">Cancel</a>
                <button type="submit" class="panel-btn-primary">Update Live Class</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const root = document.getElementById('live-class-form-fields');
    if (!root) return;

    const btn = document.getElementById('ai-analyze-btn');
    const label = document.getElementById('ai-analyze-label');
    const statusEl = document.getElementById('ai-analyze-status');
    const agendaBox = document.getElementById('ai-agenda-preview');
    const agendaList = document.getElementById('ai-agenda-list');
    const titleInput = document.getElementById('title');
    const briefInput = document.getElementById('ai_brief');

    function setStatus(msg, isError) {
        statusEl.classList.remove('hidden');
        statusEl.textContent = msg;
        statusEl.className = 'mt-2 text-xs ' + (isError ? 'text-rose-600' : 'text-emerald-700');
    }

    function setValue(name, value) {
        const el = root.querySelector('[name="' + name + '"]');
        if (!el || value === undefined || value === null) return;
        if (el.tagName === 'SELECT' || el.tagName === 'TEXTAREA' || el.tagName === 'INPUT') {
            el.value = value;
            el.dispatchEvent(new Event('change', { bubbles: true }));
            el.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }

    btn?.addEventListener('click', async function () {
        const title = (titleInput?.value || '').trim();
        if (!title) {
            setStatus('Enter a class title first, then click AI Fill Details.', true);
            titleInput?.focus();
            return;
        }

        btn.disabled = true;
        label.textContent = 'Analyzing…';
        setStatus('AI is generating description, schedule, platform & agenda…', false);

        try {
            const courseEl = root.querySelector('[name="course_id"]');
            const res = await fetch(root.dataset.aiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': root.dataset.csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    title: title,
                    brief: (briefInput?.value || '').trim() || null,
                    course_id: courseEl?.value || null,
                }),
            });

            const json = await res.json().catch(() => ({}));
            if (!res.ok || !json.ok) {
                const msg = json.message || json.errors?.title?.[0] || json.errors?.ai?.[0] || 'AI request failed.';
                throw new Error(msg);
            }

            const d = json.data || {};
            setValue('description', d.description || '');
            setValue('platform', d.platform || 'zoom');
            setValue('starts_at', d.starts_at || '');
            setValue('start_time', d.start_time || '');
            setValue('end_time', d.end_time || '');
            setValue('status', d.status || 'scheduled');
            if (d.course_id) setValue('course_id', d.course_id);

            if (Array.isArray(d.agenda_points) && d.agenda_points.length) {
                agendaList.innerHTML = d.agenda_points.map(function (item) {
                    return '<li>' + String(item).replace(/</g, '&lt;') + '</li>';
                }).join('');
                agendaBox.classList.remove('hidden');
            } else {
                agendaBox.classList.add('hidden');
            }

            setStatus(json.message || 'Details filled. Review and save the live class.', false);
        } catch (err) {
            setStatus(err.message || 'Something went wrong.', true);
        } finally {
            btn.disabled = false;
            label.textContent = 'AI Fill Details';
        }
    });
})();
</script>
@endpush
