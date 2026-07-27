@php
    $config = $settings->certificate_config ?? [];
    $presetsJson = collect($presets)->map(fn ($preset) => [
        'key' => $preset['key'],
        'name' => $preset['name'],
        'description' => $preset['description'],
        'layout' => $preset['layout'],
    ])->values();
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
    .cert-template-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }
    @media (min-width: 768px) {
        .cert-template-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    @media (min-width: 1280px) {
        .cert-template-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }
    .cert-template-card {
        display: flex;
        flex-direction: column;
        border: 2px solid #e2e8f0;
        border-radius: 1rem;
        background: #fff;
        padding: 0.75rem;
        text-align: left;
        cursor: pointer;
        transition: all .15s ease;
        min-width: 0;
    }
    .cert-template-card:hover {
        border-color: #99f6e4;
        box-shadow: 0 8px 24px rgba(13, 148, 136, 0.08);
    }
    .cert-template-card.is-selected {
        border-color: #0d9488;
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15);
    }
    /* Fixed-size thumbnail frame — all templates same outer box */
    .cert-template-thumb {
        position: relative;
        width: 100%;
        aspect-ratio: 1.414 / 1;
        border-radius: 0.65rem;
        overflow: hidden;
        background: #eef2f7;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.4rem;
        flex-shrink: 0;
    }
    .cert-template-thumb .cert-sheet {
        position: relative;
        flex-shrink: 0;
        margin: 0;
        transform: none;
        box-shadow: none;
        border-width: 4px;
        width: 100%;
        height: auto;
        aspect-ratio: 1.414 / 1;
        max-height: 100%;
    }
    .cert-template-thumb .cert-sheet.cert-portrait {
        width: auto;
        height: 100%;
        max-width: 58%;
        aspect-ratio: 1 / 1.414;
    }
    .cert-template-thumb .cert-inner {
        padding: 8% 10%;
        justify-content: center;
    }
    .cert-template-thumb .cert-inner::before { inset: 6px; }
    .cert-template-thumb .cert-title {
        font-size: 0.62rem !important;
        line-height: 1.15;
    }
    .cert-template-thumb .cert-subtitle {
        font-size: 0.34rem !important;
        margin-top: 0.35rem;
    }
    .cert-template-thumb .cert-student {
        font-size: 0.55rem !important;
        margin-top: 0.35rem;
        min-width: 0;
        border-bottom-width: 1px;
        padding-bottom: 0.15rem;
    }
    .cert-template-thumb .cert-body,
    .cert-template-thumb .cert-course,
    .cert-template-thumb .cert-signs,
    .cert-template-thumb .cert-footer {
        display: none !important;
    }
    .cert-template-thumb .cert-ornament {
        width: 38%;
        height: 38%;
    }
    .cert-preview-panel {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1.25rem;
    }
    .cert-preview-panel .cert-canvas {
        width: min(100%, 860px);
        margin: 0 auto;
    }
    .cert-preview-panel .cert-sheet {
        max-width: 100%;
    }
    .cert-editor-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 1rem;
        padding: 0.75rem 1rem;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
    }
    .cert-editor .cert-draggable {
        cursor: grab;
        touch-action: none;
        position: relative;
        z-index: 3;
        transition: outline-color .12s ease;
    }
    .cert-editor .cert-draggable:hover {
        outline: 1px dashed rgba(13, 148, 136, 0.45);
        outline-offset: 3px;
    }
    .cert-editor .cert-draggable.is-active {
        cursor: grabbing;
        outline: 2px solid #0d9488;
        outline-offset: 3px;
        z-index: 8;
    }
    .cert-position-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 0.5rem;
        margin-top: 1rem;
    }
    .cert-position-chip {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        padding: 0.5rem 0.65rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.65rem;
        background: #fff;
        font-size: 0.72rem;
    }
    .cert-position-chip.is-active {
        border-color: #0d9488;
        background: #f0fdfa;
    }
    .cert-nudge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.65rem;
        height: 1.65rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.45rem;
        background: #fff;
        color: #475569;
        font-size: 0.85rem;
        line-height: 1;
    }
    .cert-nudge:hover { background: #f0fdfa; border-color: #99f6e4; color: #0d9488; }
</style>
@endpush

<div>
    <h1 class="text-2xl font-bold text-slate-900">{{ $meta['title'] }}</h1>
    <p class="text-sm text-slate-500 mt-1">{{ $meta['description'] }} Choose a certificate design for this course.</p>
</div>

<form method="POST" action="{{ route('admin.courses.settings.update', [$course, $panel]) }}" class="mt-6 space-y-6"
      x-data="certificateTemplatePicker(@js([
          'preset_key' => old('preset_key', $selectedPresetKey),
          'enabled' => (bool) old('certificate_enabled', $settings->certificate_enabled),
          'expiry_days' => old('expiry_days', $config['expiry_days'] ?? ''),
          'auto_generate' => (bool) old('auto_generate', $config['auto_generate'] ?? true),
          'unique_number' => (bool) old('unique_number', $config['unique_number'] ?? true),
          'qr_verification' => (bool) old('qr_verification', $config['qr_verification'] ?? true),
          'course_name' => $course->title,
          'presets' => $presetsJson,
          'positions' => old('element_positions') ? json_decode(old('element_positions'), true) : $elementPositions,
          'elementLabels' => $elementLabels,
      ]))"
      @change="markDirty()" @input="markDirty()">
    @csrf
    @method('PUT')
    <input type="hidden" name="preset_key" :value="preset_key">
    <input type="hidden" name="element_positions" :value="JSON.stringify(positions)">

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

    <div class="space-y-5" :class="enabled ? '' : 'opacity-50 pointer-events-none'">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Certificate templates</h2>
                <p class="text-xs text-slate-500 mt-0.5">Pick one design — learners receive the selected template when they issue their certificate.</p>
            </div>
            <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700">{{ $issuedCount }} issued</span>
        </div>

        <div class="cert-template-grid">
            <template x-for="preset in presets" :key="preset.key">
                <button type="button"
                        class="cert-template-card"
                        :class="preset_key === preset.key ? 'is-selected' : ''"
                        @click="selectPreset(preset.key)">
                    <div class="cert-template-thumb">
                        <div class="cert-sheet"
                             :class="{
                                'cert-theme-emerald': preset.layout.theme === 'emerald',
                                'cert-theme-minimal': preset.layout.theme === 'minimal',
                                'cert-portrait': preset.layout.orientation === 'A4-portrait'
                             }"
                             :style="`--cert-primary:${preset.layout.primary_color};--cert-accent:${preset.layout.accent_color};--cert-paper:${preset.layout.paper_color}`">
                            <div class="cert-inner">
                                <div class="cert-ornament cert-ornament-tl"></div>
                                <div class="cert-ornament cert-ornament-br"></div>
                                <p class="cert-title" x-text="preset.layout.title"></p>
                                <p class="cert-subtitle" x-text="preset.layout.subtitle"></p>
                                <p class="cert-student">Student Name</p>
                            </div>
                        </div>
                    </div>
                    <p class="mt-2 text-sm font-semibold text-slate-800" x-text="preset.name"></p>
                    <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-2" x-text="preset.description"></p>
                    <span x-show="preset_key === preset.key"
                          class="inline-flex items-center gap-1 mt-2 text-[11px] font-semibold text-teal-700">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Selected
                    </span>
                </button>
            </template>
        </div>

        <div class="cert-preview-panel cert-editor">
            <div class="flex items-center justify-between gap-3 mb-2">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Position text</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Drag any text block to move it up, down, left, or right. Use arrow buttons for fine adjustments.</p>
                </div>
                <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-white border border-slate-200 text-slate-600" x-text="selectedPreset()?.name"></span>
            </div>

            <div class="cert-editor-toolbar">
                <p class="text-xs text-slate-600">
                    <span class="font-semibold text-teal-700" x-text="elementLabels[activeElement] || 'Click a text block'"></span>
                    <span x-show="activeElement" x-cloak> — drag on certificate or use arrows</span>
                </p>
                <button type="button" @click="resetPositions()"
                        class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50">
                    Reset positions
                </button>
            </div>

            <div class="cert-canvas">
                <div class="cert-sheet"
                     :class="{
                        'cert-theme-emerald': selectedLayout().theme === 'emerald',
                        'cert-theme-minimal': selectedLayout().theme === 'minimal',
                        'cert-portrait': selectedLayout().orientation === 'A4-portrait'
                     }"
                     :style="`--cert-primary:${selectedLayout().primary_color};--cert-accent:${selectedLayout().accent_color};--cert-paper:${selectedLayout().paper_color}`">
                    <div class="cert-inner">
                        <div class="cert-ornament cert-ornament-tl"></div>
                        <div class="cert-ornament cert-ornament-br"></div>
                        <p class="cert-title cert-draggable"
                           :class="activeElement === 'title' && 'is-active'"
                           :style="elementStyle('title')"
                           @mousedown.prevent="startDrag('title', $event)"><span x-text="selectedLayout().title"></span></p>
                        <p class="cert-subtitle cert-draggable"
                           :class="activeElement === 'subtitle' && 'is-active'"
                           :style="elementStyle('subtitle')"
                           @mousedown.prevent="startDrag('subtitle', $event)"><span x-text="selectedLayout().subtitle"></span></p>
                        <p class="cert-student cert-draggable"
                           :class="activeElement === 'student' && 'is-active'"
                           :style="elementStyle('student')"
                           @mousedown.prevent="startDrag('student', $event)">Student Name</p>
                        <p class="cert-body cert-draggable"
                           :class="activeElement === 'body' && 'is-active'"
                           :style="elementStyle('body')"
                           @mousedown.prevent="startDrag('body', $event)"><span x-text="selectedLayout().body"></span></p>
                        <p class="cert-course cert-draggable"
                           :class="activeElement === 'course' && 'is-active'"
                           :style="elementStyle('course')"
                           @mousedown.prevent="startDrag('course', $event)"><span x-text="course_name"></span></p>
                        <div class="cert-signs">
                            <div class="cert-sign cert-draggable"
                                 :class="activeElement === 'left_sign' && 'is-active'"
                                 :style="elementStyle('left_sign')"
                                 @mousedown.prevent="startDrag('left_sign', $event)">
                                <div class="cert-sign-line"></div>
                                <span x-text="selectedLayout().left_signatory"></span>
                            </div>
                            <div class="cert-sign cert-draggable"
                                 :class="activeElement === 'right_sign' && 'is-active'"
                                 :style="elementStyle('right_sign')"
                                 @mousedown.prevent="startDrag('right_sign', $event)">
                                <div class="cert-sign-line"></div>
                                <span x-text="selectedLayout().right_signatory"></span>
                            </div>
                        </div>
                        <div class="cert-footer cert-draggable"
                             :class="activeElement === 'footer' && 'is-active'"
                             :style="elementStyle('footer')"
                             @mousedown.prevent="startDrag('footer', $event)">
                            <div class="cert-footer-item">Verify at: <strong>learnyst.com/verify</strong></div>
                            <div class="cert-footer-item">Certificate Number: <strong>CERT-000001</strong></div>
                        </div>
                    </div>
                    @include('certificates.partials.corner-assets', [
                        'verifyUrl' => url('/verify-certificate?number=CERT-PREVIEW123'),
                        'showQr' => (bool) ($config['qr_verification'] ?? true),
                    ])
                </div>
            </div>

            <div class="cert-position-list">
                <template x-for="(label, key) in elementLabels" :key="key">
                    <div class="cert-position-chip"
                         :class="activeElement === key && 'is-active'"
                         @click="activeElement = key">
                        <span class="font-semibold text-slate-700" x-text="label"></span>
                        <div class="flex items-center gap-1 mt-1">
                            <button type="button" class="cert-nudge" @click="nudge(key, 0, -4)">↑</button>
                            <button type="button" class="cert-nudge" @click="nudge(key, 0, 4)">↓</button>
                            <button type="button" class="cert-nudge" @click="nudge(key, -4, 0)">←</button>
                            <button type="button" class="cert-nudge" @click="nudge(key, 4, 0)">→</button>
                        </div>
                        <span class="text-[10px] text-slate-400" x-text="`X ${positions[key]?.x || 0}, Y ${positions[key]?.y || 0}`"></span>
                    </div>
                </template>
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
function certificateTemplatePicker(initial) {
    const defaultPositions = () => ({
        title: { x: 0, y: 0 },
        subtitle: { x: 0, y: 0 },
        student: { x: 0, y: 0 },
        body: { x: 0, y: 0 },
        course: { x: 0, y: 0 },
        left_sign: { x: 0, y: 0 },
        right_sign: { x: 0, y: 0 },
        footer: { x: 0, y: 0 },
    });

    return {
        ...initial,
        positions: { ...defaultPositions(), ...(initial.positions || {}) },
        activeElement: 'title',
        dragging: null,
        selectPreset(key) {
            if (this.preset_key !== key) {
                this.positions = defaultPositions();
            }
            this.preset_key = key;
            this.markDirty();
        },
        selectedPreset() {
            return this.presets.find(p => p.key === this.preset_key) || this.presets[0];
        },
        selectedLayout() {
            return this.selectedPreset()?.layout || {};
        },
        elementStyle(key) {
            const pos = this.positions[key] || { x: 0, y: 0 };
            return `transform: translate(${pos.x}px, ${pos.y}px)`;
        },
        startDrag(key, event) {
            this.activeElement = key;
            this.dragging = {
                key,
                startX: event.clientX,
                startY: event.clientY,
                origX: this.positions[key]?.x || 0,
                origY: this.positions[key]?.y || 0,
            };
            this._onDrag = (e) => this.onDrag(e);
            this._endDrag = () => this.endDrag();
            window.addEventListener('mousemove', this._onDrag);
            window.addEventListener('mouseup', this._endDrag);
        },
        onDrag(event) {
            if (!this.dragging) return;
            const dx = event.clientX - this.dragging.startX;
            const dy = event.clientY - this.dragging.startY;
            const key = this.dragging.key;
            this.positions[key] = {
                x: this.clamp(this.dragging.origX + dx),
                y: this.clamp(this.dragging.origY + dy),
            };
            this.markDirty();
        },
        endDrag() {
            this.dragging = null;
            window.removeEventListener('mousemove', this._onDrag);
            window.removeEventListener('mouseup', this._endDrag);
        },
        nudge(key, dx, dy) {
            this.activeElement = key;
            const current = this.positions[key] || { x: 0, y: 0 };
            this.positions[key] = {
                x: this.clamp((current.x || 0) + dx),
                y: this.clamp((current.y || 0) + dy),
            };
            this.markDirty();
        },
        resetPositions() {
            this.positions = defaultPositions();
            this.markDirty();
        },
        clamp(value) {
            return Math.max(-280, Math.min(280, Math.round(value)));
        },
    };
}
</script>
@endpush
