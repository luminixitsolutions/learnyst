@php
    $config = $settings->certificate_config ?? [];
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

<div>
    <h1 class="text-2xl font-bold text-slate-900">{{ $meta['title'] }}</h1>
    <p class="text-sm text-slate-500 mt-1">{{ $meta['description'] }}</p>
</div>

<form method="POST" action="{{ route('admin.courses.settings.update', [$course, $panel]) }}" class="mt-8 space-y-6"
      x-data="{ enabled: {{ old('certificate_enabled', $settings->certificate_enabled) ? 'true' : 'false' }} }"
      @change="markDirty()" x-on:input="markDirty()">
    @csrf
    @method('PUT')

    <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
        <div>
            <p class="text-sm font-medium text-slate-800">Enable certificates</p>
            <p class="text-xs text-slate-500">Issue certificates when learners meet criteria</p>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" name="certificate_enabled" value="1" class="sr-only peer" x-model="enabled">
            <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-slate-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
        </label>
    </div>

    <div class="space-y-5 rounded-xl border border-slate-200 p-5 transition"
         :class="enabled ? '' : 'opacity-50 pointer-events-none bg-slate-50'">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Certificate configuration</h2>
            <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700">{{ $issuedCount }} issued</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Template</label>
                <select name="certificate_template_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
                    <option value="">Select template</option>
                    @foreach($templates as $template)
                        <option value="{{ $template->id }}" @selected(old('certificate_template_id', $config['certificate_template_id'] ?? null) == $template->id)>{{ $template->name }}</option>
                    @endforeach
                </select>
                @error('certificate_template_id')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Expiry days</label>
                <input type="number" name="expiry_days" min="1" value="{{ old('expiry_days', $config['expiry_days'] ?? '') }}" placeholder="Leave blank for no expiry"
                       class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
                @error('expiry_days')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            @foreach([
                'auto_generate' => ['Auto generate', true],
                'unique_number' => ['Unique certificate number', true],
                'qr_verification' => ['QR verification', true],
            ] as $field => [$label, $default])
                <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
                    <p class="text-sm font-medium text-slate-800">{{ $label }}</p>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="{{ $field }}" value="1" class="sr-only peer" @checked(old($field, $config[$field] ?? $default))>
                        <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-slate-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                    </label>
                </div>
            @endforeach
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
        @error('criterion_type')<p class="text-xs text-rose-600 mt-2">{{ $message }}</p>@enderror
    </div>
</div>
