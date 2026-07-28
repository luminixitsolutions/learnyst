@extends('layouts.app')

@section('title', 'Sub Admin Wizard — Details')
@section('page-title', 'Create Sub Admin')
@section('breadcrumb', 'Wizard / Step 1 — Details')

@section('content')
<div class="max-w-2xl mx-auto">
    @include('admin.sub-admins.wizard._progress', ['current' => 1])

    <div class="glass-card rounded-2xl p-6" id="sub-admin-form-fields"
         data-ai-url="{{ route('admin.sub-admins.wizard.ai-analyze') }}"
         data-csrf="{{ csrf_token() }}">
        <form method="POST" action="{{ route('admin.sub-admins.wizard.store', 1) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <x-form-input label="Full Name" name="name" :value="old('name', $data['details']['name'] ?? '')" required />
            <x-form-input label="Email" name="email" type="email" :value="old('email', $data['details']['email'] ?? '')" required />
            <x-form-input label="Phone" name="phone" :value="old('phone', $data['details']['phone'] ?? '')" />
            <x-form-input label="Password" name="password" type="password" required />

            <div class="rounded-xl border border-indigo-100 bg-indigo-50/60 p-4 space-y-3">
                <div class="flex flex-col md:flex-row md:items-end gap-3">
                    <div class="flex-1 space-y-1.5">
                        <label for="expertise" class="block text-sm font-semibold text-slate-700">Designation</label>
                        <input type="text" name="expertise" id="expertise" value="{{ old('expertise', $data['details']['expertise'] ?? '') }}"
                               placeholder="e.g. Academic Coordinator, Course Manager, Support Lead"
                               class="panel-input w-full">
                        @error('expertise')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="button" id="ai-analyze-btn"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shrink-0 disabled:opacity-60 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span id="ai-analyze-label">AI Fill Details</span>
                    </button>
                </div>
                <div class="space-y-1.5">
                    <label for="ai_brief" class="block text-xs font-medium text-slate-600">Optional notes for AI (team focus, experience)</label>
                    <input type="text" id="ai_brief" class="panel-input w-full text-sm" placeholder="e.g. Handles live-class ops, reports to academic head">
                </div>
                <p id="ai-analyze-status" class="text-xs text-slate-500 hidden"></p>
                <div id="ai-responsibilities-preview" class="hidden">
                    <p class="text-xs font-semibold text-slate-600 mb-1">Suggested responsibilities</p>
                    <ul id="ai-responsibilities-list" class="text-xs text-slate-600 list-disc pl-5 space-y-0.5"></ul>
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="bio" class="block text-sm font-semibold text-slate-700">Notes / Bio</label>
                <textarea name="bio" id="bio" rows="5" class="panel-input" placeholder="Short staff profile based on designation…">{{ old('bio', $data['details']['bio'] ?? '') }}</textarea>
                @error('bio')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-300">Avatar</label>
                <input type="file" name="avatar" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-slate-800 file:text-slate-300">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach(['facebook', 'linkedin', 'twitter', 'website'] as $platform)
                <x-form-input :label="ucfirst($platform)" :name="'social_links['.$platform.']'" :value="old('social_links.'.$platform, $data['details']['social_links'][$platform] ?? '')" placeholder="https://" />
                @endforeach
            </div>
            <div class="flex justify-between pt-4 border-t border-slate-200">
                <a href="{{ route('admin.sub-admins.index') }}" class="text-sm text-slate-500 hover:text-white">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Next: Role →</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const root = document.getElementById('sub-admin-form-fields');
    if (!root) return;

    const btn = document.getElementById('ai-analyze-btn');
    const label = document.getElementById('ai-analyze-label');
    const statusEl = document.getElementById('ai-analyze-status');
    const respBox = document.getElementById('ai-responsibilities-preview');
    const respList = document.getElementById('ai-responsibilities-list');

    function setStatus(msg, isError) {
        statusEl.classList.remove('hidden');
        statusEl.textContent = msg;
        statusEl.className = 'text-xs ' + (isError ? 'text-rose-600' : 'text-emerald-700');
    }

    function setValue(name, value) {
        const el = root.querySelector('[name="' + name + '"]');
        if (!el || value === undefined || value === null || value === '') return;
        el.value = value;
        el.dispatchEvent(new Event('change', { bubbles: true }));
        el.dispatchEvent(new Event('input', { bubbles: true }));
    }

    btn?.addEventListener('click', async function () {
        const name = (root.querySelector('[name="name"]')?.value || '').trim();
        const designation = (root.querySelector('[name="expertise"]')?.value || '').trim();

        if (!name) {
            setStatus('Enter the full name first.', true);
            root.querySelector('[name="name"]')?.focus();
            return;
        }
        if (!designation) {
            setStatus('Enter a designation first, then click AI Fill Details.', true);
            root.querySelector('[name="expertise"]')?.focus();
            return;
        }

        btn.disabled = true;
        label.textContent = 'Analyzing…';
        setStatus('AI is generating bio, email suggestion & responsibilities…', false);

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
                    designation: designation,
                    brief: (document.getElementById('ai_brief')?.value || '').trim() || null,
                }),
            });

            const json = await res.json().catch(() => ({}));
            if (!res.ok || !json.ok) {
                throw new Error(json.message || json.errors?.designation?.[0] || json.errors?.ai?.[0] || 'AI request failed.');
            }

            const d = json.data || {};
            setValue('bio', d.bio || '');

            const emailEl = root.querySelector('[name="email"]');
            if (emailEl && !(emailEl.value || '').trim() && d.suggested_email) {
                setValue('email', d.suggested_email);
            }

            const points = d.responsibility_points || [];
            if (points.length) {
                respList.innerHTML = points.map(function (item) {
                    return '<li>' + String(item).replace(/</g, '&lt;') + '</li>';
                }).join('');
                respBox.classList.remove('hidden');
            } else {
                respBox.classList.add('hidden');
            }

            setStatus(json.message || 'Details filled. Review and continue to Role.', false);
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
