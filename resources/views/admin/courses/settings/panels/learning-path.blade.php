@php
    $config = $settings->learning_path_config ?? [];
    $hasActiveEnrollments = $course->enrollments()->where('status', 'active')->exists();
@endphp

<div>
    <h1 class="text-2xl font-bold text-slate-900">{{ $meta['title'] }}</h1>
    <p class="text-sm text-slate-500 mt-1">{{ $meta['description'] }}</p>
</div>

<form method="POST" action="{{ route('admin.courses.settings.update', [$course, $panel]) }}" class="mt-8 space-y-6"
      x-data="{ enabled: {{ old('learning_path_enabled', $settings->learning_path_enabled) ? 'true' : 'false' }} }"
      @change="markDirty()" x-on:input="markDirty()">
    @csrf
    @method('PUT')

    <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
        <div>
            <p class="text-sm font-medium text-slate-800">Enable learning path</p>
            <p class="text-xs text-slate-500">Enforce sequential unlock rules for lessons and sections</p>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" name="learning_path_enabled" value="1" class="sr-only peer" x-model="enabled">
            <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-slate-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
        </label>
    </div>

    @if($hasActiveEnrollments)
        <div x-show="!enabled" x-cloak class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 space-y-2">
            <p class="text-sm text-amber-800">This course has active enrollments. Confirm before disabling the learning path.</p>
            <label class="flex items-center gap-2 text-sm text-amber-900">
                <input type="checkbox" name="confirm_disable" value="1" class="rounded border-amber-300 text-emerald-600 focus:ring-emerald-500">
                I understand learners may gain unrestricted access to locked content
            </label>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        @foreach([
            'sequential' => ['Sequential path', 'Learners must follow the defined order', true],
            'lesson_lock' => ['Lesson lock', 'Lock lessons until prerequisites are met', true],
            'section_lock' => ['Section lock', 'Lock sections until previous sections are complete', false],
            'unlock_after_completion' => ['Unlock after completion', 'Unlock next item after completing current', true],
            'unlock_after_quiz' => ['Unlock after quiz', 'Require quiz pass to unlock next', false],
            'unlock_after_assignment' => ['Unlock after assignment', 'Require assignment submission to unlock next', false],
            'allow_optional' => ['Allow optional items', 'Optional lessons can be skipped', true],
        ] as $field => [$title, $help, $default])
            <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
                <div>
                    <p class="text-sm font-medium text-slate-800">{{ $title }}</p>
                    <p class="text-xs text-slate-500">{{ $help }}</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="{{ $field }}" value="1" class="sr-only peer" @checked(old($field, $config[$field] ?? $default))>
                    <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-slate-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                </label>
            </div>
        @endforeach
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Minimum section progress (%)</label>
        <input type="number" name="min_section_progress" min="0" max="100"
               value="{{ old('min_section_progress', $config['min_section_progress'] ?? 100) }}"
               class="w-full max-w-xs rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
        @error('min_section_progress')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
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
