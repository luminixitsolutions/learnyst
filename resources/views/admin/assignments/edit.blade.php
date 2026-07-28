@extends('layouts.app')

@section('title', 'Edit Assignment')
@section('page-title', 'Edit Assignment')
@section('breadcrumb', 'Assignments / Edit')

@section('content')
@php
    $assignmentData = $assignment->quiz_data ?? [];
    $selectedCourseId = old('course_id', $assignment->section?->course_id);
    $selectedSectionId = old('section_id', $assignment->course_section_id);
@endphp
<div class="max-w-3xl" x-data="{ courseId: '{{ $selectedCourseId }}', sections: @js($courses->mapWithKeys(fn($c) => [$c->id => $c->sections->map(fn($s) => ['id' => $s->id, 'title' => $s->title])])) }">
    <div class="glass-card rounded-2xl p-6" id="assignment-form-fields"
         data-ai-url="{{ route('admin.assignments.ai-analyze') }}"
         data-csrf="{{ csrf_token() }}">
        <form method="POST" action="{{ route('admin.assignments.update', $assignment) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="rounded-xl border border-indigo-100 bg-indigo-50/60 p-4">
                <div class="flex flex-col md:flex-row md:items-end gap-3">
                    <div class="flex-1 space-y-1.5">
                        <label for="title" class="block text-sm font-semibold text-slate-700">Assignment Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title', $assignment->title) }}" required
                               placeholder="e.g. Build a REST API with Laravel"
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
                    <label for="ai_brief" class="block text-xs font-medium text-slate-600">Optional notes for AI (level, focus, length)</label>
                    <input type="text" id="ai_brief" class="panel-input w-full text-sm" placeholder="e.g. Intermediate, 1 week, include rubric for code quality">
                </div>
                <p id="ai-analyze-status" class="mt-2 text-xs text-slate-500 hidden"></p>
                <div id="ai-rubric-preview" class="mt-3 hidden">
                    <p class="text-xs font-semibold text-slate-600 mb-1">Suggested rubric</p>
                    <ul id="ai-rubric-list" class="text-xs text-slate-600 list-disc pl-5 space-y-0.5"></ul>
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="course_id" class="block text-sm font-semibold text-slate-700">Course <span class="text-red-500">*</span></label>
                <select name="course_id" id="course_id" required class="panel-select w-full" x-model="courseId">
                    <option value="">Select course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" @selected((string) $selectedCourseId === (string) $course->id)>{{ $course->title }}</option>
                    @endforeach
                </select>
                @error('course_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700">Lesson / Section</label>
                <select name="section_id" required class="panel-input">
                    <option value="">Select section</option>
                    <template x-for="section in sections[courseId] || []" :key="section.id">
                        <option :value="section.id" x-text="section.title" :selected="section.id == '{{ $selectedSectionId }}'"></option>
                    </template>
                </select>
            </div>
            <div class="space-y-1.5">
                <label for="description" class="block text-sm font-semibold text-slate-700">Description</label>
                <textarea name="description" id="description" rows="8" class="panel-input">{{ old('description', $assignment->content) }}</textarea>
                @error('description')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-input label="Due Date" name="due_date" type="date" :value="old('due_date', $assignmentData['due_date'] ?? '')" />
                <x-form-input label="Marks" name="marks" type="number" step="0.01" :value="old('marks', $assignmentData['marks'] ?? '')" />
                <x-form-input label="Status" name="status" type="select" required>
                    <option value="draft" @selected(old('status', $assignmentData['status'] ?? 'published') === 'draft')>Draft</option>
                    <option value="published" @selected(old('status', $assignmentData['status'] ?? 'published') === 'published')>Published</option>
                </x-form-input>
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700">Upload File</label>
                @if($assignment->file_path)
                    <p class="text-sm text-slate-500 mb-2">Current file: {{ basename($assignment->file_path) }}</p>
                @endif
                <input type="file" name="file_path" class="w-full text-sm text-slate-500">
            </div>
            <div class="flex justify-between pt-4 border-t">
                <a href="{{ route('admin.assignments.index') }}" class="text-sm text-slate-500">Cancel</a>
                <button type="submit" class="panel-btn-primary">Save Assignment</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const root = document.getElementById('assignment-form-fields');
    if (!root) return;

    const btn = document.getElementById('ai-analyze-btn');
    const label = document.getElementById('ai-analyze-label');
    const statusEl = document.getElementById('ai-analyze-status');
    const rubricBox = document.getElementById('ai-rubric-preview');
    const rubricList = document.getElementById('ai-rubric-list');
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
            setStatus('Enter an assignment title first, then click AI Fill Details.', true);
            titleInput?.focus();
            return;
        }

        btn.disabled = true;
        label.textContent = 'Analyzing…';
        setStatus('AI is generating description, marks, due date & rubric…', false);

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
            setValue('marks', d.marks ?? '');
            setValue('due_date', d.due_date || '');
            setValue('status', d.status || 'draft');

            if (Array.isArray(d.rubric_points) && d.rubric_points.length) {
                rubricList.innerHTML = d.rubric_points.map(function (item) {
                    return '<li>' + String(item).replace(/</g, '&lt;') + '</li>';
                }).join('');
                rubricBox.classList.remove('hidden');
            } else {
                rubricBox.classList.add('hidden');
            }

            setStatus(json.message || 'Details filled. Review and save the assignment.', false);
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
