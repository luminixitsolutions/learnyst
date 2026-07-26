@php
    /** @var \App\Services\CertificateDesignService $designService */
    $config = $settings->certificate_config ?? [];
    $layout = $designService->layoutFrom($courseTemplate);
    $previewHtml = $designService->compileHtml($layout);
    $previewReplacements = $designService->previewReplacements($course, auth()->user());
    $placeholders = \App\Services\CertificateDesignService::PLACEHOLDERS;
    $criterionTypes = [
        'complete_course' => 'Complete course',
        'complete_lessons' => 'Complete lessons',
        'pass_tests' => 'Pass tests',
        'min_percentage' => 'Minimum percentage',
        'complete_assignments' => 'Complete assignments',
        'attend_sessions' => 'Attend sessions',
        'date_range' => 'Date range',
    ];
@endphp

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&display=swap" rel="stylesheet">
@include('certificates.partials.styles')
<style>
    .cert-builder-shell { background: #e8edf5; border: 1px solid #dbe3ef; border-radius: 1rem; overflow: hidden; }
    .cert-builder-top {
        display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem;
        padding: 0.75rem 1rem; background: #fff; border-bottom: 1px solid #e2e8f0;
    }
    .cert-builder-body { display: grid; grid-template-columns: 56px 1fr 280px; min-height: 520px; }
    @media (max-width: 1024px) { .cert-builder-body { grid-template-columns: 1fr; } }
    .cert-tool-rail {
        background: #0f172a; color: #fff; display: flex; flex-direction: column; align-items: center; gap: 0.5rem; padding: 0.75rem 0.4rem;
    }
    .cert-tool-btn {
        width: 2.5rem; height: 2.5rem; border-radius: 0.7rem; border: 1px solid transparent; background: transparent; color: #cbd5e1;
        display: inline-flex; align-items: center; justify-content: center; cursor: pointer;
    }
    .cert-tool-btn:hover, .cert-tool-btn.is-active { background: #1e293b; color: #fff; border-color: #334155; }
    .cert-canvas-wrap { padding: 1.25rem; overflow: auto; display: flex; justify-content: center; align-items: flex-start; }
    .cert-canvas { width: min(100%, 860px); }
    .cert-side {
        background: #fff; border-left: 1px solid #e2e8f0; padding: 1rem; overflow: auto;
    }
    @media (max-width: 1024px) {
        .cert-tool-rail { flex-direction: row; justify-content: center; }
        .cert-side { border-left: 0; border-top: 1px solid #e2e8f0; }
    }
    .cert-field label { display: block; font-size: 0.75rem; font-weight: 600; color: #475569; margin-bottom: 0.35rem; }
    .cert-field input, .cert-field textarea, .cert-field select {
        width: 100%; border: 1px solid #e2e8f0; border-radius: 0.7rem; padding: 0.55rem 0.75rem; font-size: 0.8125rem;
        background: #fff; color: #0f172a;
    }
    .cert-field textarea { min-height: 4.5rem; resize: vertical; }
    .cert-field + .cert-field { margin-top: 0.85rem; }
    .placeholder-chip {
        display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.3rem 0.55rem; border-radius: 999px;
        background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 0.7rem; font-weight: 600; cursor: pointer;
    }
</style>
@endpush

<div>
    <h1 class="text-2xl font-bold text-slate-900">{{ $meta['title'] }}</h1>
    <p class="text-sm text-slate-500 mt-1">{{ $meta['description'] }} Design the certificate learners receive after completing this course.</p>
</div>

<form method="POST" action="{{ route('admin.courses.settings.update', [$course, $panel]) }}" class="mt-6 space-y-6"
      x-data="certificateBuilder(@js([
          'enabled' => (bool) old('certificate_enabled', $settings->certificate_enabled),
          'design_name' => old('design_name', $courseTemplate->name),
          'theme' => old('theme', $layout['theme'] ?? 'classic-blue-gold'),
          'orientation' => old('orientation', $layout['orientation'] ?? 'A4-landscape'),
          'title' => old('title', $layout['title'] ?? 'Certificate of Completion'),
          'subtitle' => old('subtitle', $layout['subtitle'] ?? 'This Certificate is Proudly Present to:'),
          'body' => old('body', $layout['body'] ?? ''),
          'left_signatory' => old('left_signatory', $layout['left_signatory'] ?? 'Head of the Department'),
          'right_signatory' => old('right_signatory', $layout['right_signatory'] ?? 'School Principal'),
          'primary_color' => old('primary_color', $layout['primary_color'] ?? '#1e4a8c'),
          'accent_color' => old('accent_color', $layout['accent_color'] ?? '#c9a227'),
          'paper_color' => old('paper_color', $layout['paper_color'] ?? '#fffef8'),
          'show_verify_url' => (bool) old('show_verify_url', $layout['show_verify_url'] ?? true),
          'show_cert_number' => (bool) old('show_cert_number', $layout['show_cert_number'] ?? true),
          'certificate_template_id' => (string) old('certificate_template_id', $courseTemplate->id),
          'expiry_days' => old('expiry_days', $config['expiry_days'] ?? ''),
          'auto_generate' => (bool) old('auto_generate', $config['auto_generate'] ?? true),
          'unique_number' => (bool) old('unique_number', $config['unique_number'] ?? true),
          'qr_verification' => (bool) old('qr_verification', $config['qr_verification'] ?? true),
          'course_name' => $course->title,
      ]))"
      @change="markDirty()" @input="markDirty()">
    @csrf
    @method('PUT')
    <input type="hidden" name="save_design" value="1">
    <input type="hidden" name="certificate_template_id" :value="certificate_template_id">

    <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
        <div>
            <p class="text-sm font-medium text-slate-800">Enable certificates</p>
            <p class="text-xs text-slate-500">Issue certificates when learners complete this course</p>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" name="certificate_enabled" value="1" class="sr-only peer" x-model="enabled">
            <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-slate-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
        </label>
    </div>

    <div class="space-y-4" :class="enabled ? '' : 'opacity-50 pointer-events-none'">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Certificate builder</h2>
                <p class="text-xs text-slate-500 mt-0.5">Same design is shown to learners after they issue their certificate.</p>
            </div>
            <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700">{{ $issuedCount }} issued</span>
        </div>

        <div class="cert-builder-shell">
            <div class="cert-builder-top">
                <div class="flex items-center gap-2">
                    <select name="orientation" x-model="orientation" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700">
                        <option value="A4-landscape">A4-landscape</option>
                        <option value="A4-portrait">A4-portrait</option>
                    </select>
                    <select name="theme" x-model="theme" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700">
                        <option value="classic-blue-gold">Classic blue & gold</option>
                        <option value="emerald">Emerald</option>
                        <option value="minimal">Minimal</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500 hidden sm:inline">Live preview with placeholders</span>
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        Save design
                    </button>
                </div>
            </div>

            <div class="cert-builder-body">
                <aside class="cert-tool-rail">
                    <button type="button" class="cert-tool-btn is-active" title="Text fields" @click="panel='text'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h14"/></svg>
                    </button>
                    <button type="button" class="cert-tool-btn" :class="panel==='placeholders' && 'is-active'" title="Placeholders" @click="panel='placeholders'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    </button>
                    <button type="button" class="cert-tool-btn" :class="panel==='style' && 'is-active'" title="Colors" @click="panel='style'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                    </button>
                </aside>

                <div class="cert-canvas-wrap">
                    <div class="cert-canvas">
                        <div class="cert-sheet"
                             :class="{
                                'cert-theme-emerald': theme === 'emerald',
                                'cert-theme-minimal': theme === 'minimal',
                                'cert-portrait': orientation === 'A4-portrait'
                             }"
                             :style="`--cert-primary:${primary_color};--cert-accent:${accent_color};--cert-paper:${paper_color}`">
                            <div class="cert-ornament cert-ornament-tl"></div>
                            <div class="cert-ornament cert-ornament-br"></div>
                            <div class="cert-inner">
                                <p class="cert-title" x-text="title"></p>
                                <p class="cert-subtitle" x-text="subtitle"></p>
                                <p class="cert-student"><span class="cert-placeholder">{student_name}</span></p>
                                <p class="cert-body" x-text="body"></p>
                                <p class="cert-course"><span class="cert-placeholder">{course_name}</span></p>
                                <div class="cert-signs">
                                    <div class="cert-sign">
                                        <div class="cert-sign-line"></div>
                                        <span x-text="left_signatory"></span>
                                    </div>
                                    <div class="cert-sign">
                                        <div class="cert-sign-line"></div>
                                        <span x-text="right_signatory"></span>
                                    </div>
                                </div>
                                <div class="cert-footer">
                                    <div class="cert-footer-item" x-show="show_verify_url">
                                        Verify at: <strong><span class="cert-placeholder">{verify_url}</span></strong>
                                    </div>
                                    <div class="cert-footer-item" x-show="show_cert_number">
                                        Certificate Number: <strong><span class="cert-placeholder">{cert_id}</span></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <aside class="cert-side">
                    <div x-show="panel === 'text'" class="space-y-1">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500 mb-3">Content</p>
                        <div class="cert-field">
                            <label>Template name</label>
                            <input type="text" name="design_name" x-model="design_name">
                        </div>
                        <div class="cert-field">
                            <label>Title</label>
                            <input type="text" name="title" x-model="title">
                        </div>
                        <div class="cert-field">
                            <label>Subtitle</label>
                            <input type="text" name="subtitle" x-model="subtitle">
                        </div>
                        <div class="cert-field">
                            <label>Body text</label>
                            <textarea name="body" x-model="body"></textarea>
                        </div>
                        <div class="cert-field">
                            <label>Left signatory</label>
                            <input type="text" name="left_signatory" x-model="left_signatory">
                        </div>
                        <div class="cert-field">
                            <label>Right signatory</label>
                            <input type="text" name="right_signatory" x-model="right_signatory">
                        </div>
                    </div>

                    <div x-show="panel === 'placeholders'" x-cloak>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500 mb-3">Dynamic fields</p>
                        <p class="text-xs text-slate-500 mb-3">These placeholders are replaced with real learner data when the certificate is issued.</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($placeholders as $key => $label)
                                <button type="button" class="placeholder-chip" @click="copyPlaceholder('{{ $key }}')" title="{{ $label }}">{ {{ $key }} }</button>
                            @endforeach
                        </div>
                        <div class="mt-4 space-y-3">
                            <label class="flex items-center justify-between gap-3 text-sm text-slate-700">
                                <span>Show verify URL</span>
                                <input type="checkbox" name="show_verify_url" value="1" x-model="show_verify_url" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            </label>
                            <label class="flex items-center justify-between gap-3 text-sm text-slate-700">
                                <span>Show certificate number</span>
                                <input type="checkbox" name="show_cert_number" value="1" x-model="show_cert_number" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            </label>
                        </div>
                        <p class="text-xs text-emerald-700 mt-3" x-show="copied" x-cloak>Copied placeholder</p>
                    </div>

                    <div x-show="panel === 'style'" x-cloak>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500 mb-3">Colors</p>
                        <div class="cert-field">
                            <label>Primary</label>
                            <input type="color" name="primary_color" x-model="primary_color">
                        </div>
                        <div class="cert-field">
                            <label>Accent (gold / trim)</label>
                            <input type="color" name="accent_color" x-model="accent_color">
                        </div>
                        <div class="cert-field">
                            <label>Paper</label>
                            <input type="color" name="paper_color" x-model="paper_color">
                        </div>
                    </div>
                </aside>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 rounded-xl border border-slate-200 p-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Expiry days</label>
                <input type="number" name="expiry_days" min="1" x-model="expiry_days" placeholder="Leave blank for no expiry"
                       class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
            </div>
            <div class="grid grid-cols-1 gap-3 content-start">
                @foreach([
                    'auto_generate' => 'Auto generate',
                    'unique_number' => 'Unique certificate number',
                    'qr_verification' => 'QR verification',
                ] as $field => $label)
                    <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
                        <p class="text-sm font-medium text-slate-800">{{ $label }}</p>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="{{ $field }}" value="1" class="sr-only peer" x-model="{{ $field }}">
                            <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-slate-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="sticky bottom-0 -mx-6 md:-mx-8 mt-10 px-6 md:px-8 py-4 border-t border-slate-200 bg-white flex items-center justify-end gap-3 rounded-b-2xl">
        <a href="{{ route('admin.courses.settings.hub', $course) }}"
           @click.prevent="requestLeave('{{ route('admin.courses.settings.hub', $course) }}')"
           class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</a>
        <button type="submit" :disabled="!dirty"
                class="px-5 py-2.5 rounded-xl text-sm font-semibold transition disabled:cursor-not-allowed"
                :class="dirty ? 'bg-slate-900 text-white hover:bg-slate-800' : 'bg-slate-100 text-slate-400'">Save</button>
    </div>
</form>

<div class="mt-10 space-y-6">
    <div>
        <h2 class="text-sm font-bold text-slate-900 mb-3">Issuance criteria</h2>
        @if($criteria->isEmpty())
            <div class="rounded-xl border border-dashed border-slate-200 p-6 text-center text-sm text-slate-500">No criteria configured yet.</div>
        @else
            <div class="space-y-2">
                @foreach($criteria as $criterion)
                    <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 px-4 py-3">
                        <div>
                            <p class="text-sm font-medium text-slate-900">{{ $criterionTypes[$criterion->criterion_type] ?? $criterion->criterion_type }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Logic: {{ strtoupper($criterion->logic) }}
                                · {{ $criterion->is_mandatory ? 'Mandatory' : 'Optional' }}
                            </p>
                        </div>
                        <form method="POST" action="{{ route('admin.courses.settings.criteria.destroy', [$course, $criterion]) }}" onsubmit="return confirm('Remove this criterion?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-medium text-rose-600 hover:text-rose-700">Delete</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="rounded-xl border border-slate-200 p-5">
        <h3 class="text-sm font-bold text-slate-900 mb-4">Add criterion</h3>
        <form method="POST" action="{{ route('admin.courses.settings.criteria.store', $course) }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Type</label>
                <select name="criterion_type" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    @foreach($criterionTypes as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Logic</label>
                <select name="logic" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    <option value="and">AND</option>
                    <option value="or">OR</option>
                </select>
            </div>
            <div class="flex items-center gap-2 pb-2">
                <input type="checkbox" name="is_mandatory" value="1" checked class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                <label class="text-sm text-slate-700">Mandatory</label>
            </div>
            <button type="submit" class="px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">Add</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function certificateBuilder(initial) {
    return {
        ...initial,
        panel: 'text',
        copied: false,
        copyPlaceholder(key) {
            const text = '{' + key + '}';
            if (navigator.clipboard) navigator.clipboard.writeText(text);
            this.copied = true;
            setTimeout(() => this.copied = false, 1500);
        }
    }
}
</script>
@endpush
