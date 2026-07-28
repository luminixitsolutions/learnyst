@extends('layouts.app')

@section('title', 'Edit Instructor')
@section('page-title', 'Edit Instructor')
@section('breadcrumb', $instructor->name)

@section('content')
<div class="max-w-2xl">
    <div class="glass-card rounded-2xl p-6" id="instructor-form-fields"
         data-ai-url="{{ route('admin.instructors.ai-analyze') }}"
         data-csrf="{{ csrf_token() }}">
        <form method="POST" action="{{ route('admin.instructors.update', $instructor) }}" class="space-y-5">
            @csrf
            @method('PUT')
            <x-form-input label="Full Name" name="name" :value="$instructor->name" required />
            <x-form-input label="Email" name="email" type="email" :value="$instructor->email" required />
            <x-form-input label="Phone" name="phone" :value="$instructor->phone" />

            <div class="rounded-xl border border-indigo-100 bg-indigo-50/60 p-4 space-y-3">
                <div class="flex flex-col md:flex-row md:items-end gap-3">
                    <div class="flex-1 space-y-1.5">
                        <label for="expertise" class="block text-sm font-semibold text-slate-700">Profession</label>
                        <input type="text" name="expertise" id="expertise" value="{{ old('expertise', $instructor->expertise) }}"
                               placeholder="e.g. Full-stack Developer, Math Teacher, Digital Marketer"
                               class="panel-input w-full">
                        @error('expertise')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="button" id="ai-analyze-btn"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shrink-0 disabled:opacity-60 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span id="ai-analyze-label">AI Write Bio</span>
                    </button>
                </div>
                <div class="space-y-1.5">
                    <label for="ai_brief" class="block text-xs font-medium text-slate-600">Optional notes for AI (experience, teaching style, specialties)</label>
                    <input type="text" id="ai_brief" class="panel-input w-full text-sm" placeholder="e.g. 8 years industry exp, project-based teaching, Java & Spring">
                </div>
                <p id="ai-analyze-status" class="text-xs text-slate-500 hidden"></p>
                <div id="ai-highlights-preview" class="hidden">
                    <p class="text-xs font-semibold text-slate-600 mb-1">Suggested highlights</p>
                    <ul id="ai-highlights-list" class="text-xs text-slate-600 list-disc pl-5 space-y-0.5"></ul>
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="bio" class="block text-sm font-semibold text-slate-700">Bio</label>
                <textarea name="bio" id="bio" rows="6" class="panel-input" placeholder="Short instructor profile…">{{ old('bio', $instructor->bio) }}</textarea>
                @error('bio')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <label class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $instructor->is_active ?? true)) class="rounded border-slate-600 bg-slate-800 text-brand-500">
                <span class="text-sm text-slate-300">Active account</span>
            </label>
            <div class="flex justify-between pt-4">
                <a href="{{ route('admin.instructors.show', $instructor) }}" class="text-sm text-slate-500">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const root = document.getElementById('instructor-form-fields');
    if (!root) return;

    const btn = document.getElementById('ai-analyze-btn');
    const label = document.getElementById('ai-analyze-label');
    const statusEl = document.getElementById('ai-analyze-status');
    const highlightsBox = document.getElementById('ai-highlights-preview');
    const highlightsList = document.getElementById('ai-highlights-list');

    function setStatus(msg, isError) {
        statusEl.classList.remove('hidden');
        statusEl.textContent = msg;
        statusEl.className = 'text-xs ' + (isError ? 'text-rose-600' : 'text-emerald-700');
    }

    btn?.addEventListener('click', async function () {
        const name = (root.querySelector('[name="name"]')?.value || '').trim();
        const profession = (root.querySelector('[name="expertise"]')?.value || '').trim();

        if (!name) {
            setStatus('Enter the instructor name first.', true);
            root.querySelector('[name="name"]')?.focus();
            return;
        }
        if (!profession) {
            setStatus('Enter a profession first, then click AI Write Bio.', true);
            root.querySelector('[name="expertise"]')?.focus();
            return;
        }

        btn.disabled = true;
        label.textContent = 'Writing…';
        setStatus('AI is writing a bio based on the profession…', false);

        try {
            const res = await fetch(root.dataset.aiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': root.dataset.csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    name: name,
                    profession: profession,
                    brief: (document.getElementById('ai_brief')?.value || '').trim() || null,
                }),
            });

            const json = await res.json().catch(() => ({}));
            if (!res.ok || !json.ok) {
                throw new Error(json.message || json.errors?.profession?.[0] || json.errors?.ai?.[0] || 'AI request failed.');
            }

            const bioEl = root.querySelector('[name="bio"]');
            if (bioEl) {
                bioEl.value = json.data?.bio || '';
                bioEl.dispatchEvent(new Event('input', { bubbles: true }));
            }

            const points = json.data?.highlight_points || [];
            if (points.length) {
                highlightsList.innerHTML = points.map(function (item) {
                    return '<li>' + String(item).replace(/</g, '&lt;') + '</li>';
                }).join('');
                highlightsBox.classList.remove('hidden');
            } else {
                highlightsBox.classList.add('hidden');
            }

            setStatus(json.message || 'Bio filled. Review and save.', false);
        } catch (err) {
            setStatus(err.message || 'Something went wrong.', true);
        } finally {
            btn.disabled = false;
            label.textContent = 'AI Write Bio';
        }
    });
})();
</script>
@endpush
