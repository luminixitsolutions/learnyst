@php
    $config = $settings->learner_config ?? [];
@endphp

<div>
    <h1 class="text-2xl font-bold text-slate-900">{{ $meta['title'] }}</h1>
    <p class="text-sm text-slate-500 mt-1">{{ $meta['description'] }}</p>
</div>

<form method="POST" action="{{ route('admin.courses.settings.update', [$course, $panel]) }}" class="mt-8 space-y-6" @change="markDirty()" x-on:input="markDirty()">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        @foreach([
            'request_completion' => ['Request completion', 'Learners must request course completion', false],
            'mark_complete' => ['Mark complete', 'Learners can mark lessons complete', true],
            'require_quiz' => ['Require quiz', 'Quizzes must be passed to progress', false],
            'require_assignment' => ['Require assignment', 'Assignments must be submitted to progress', false],
            'restrict_skipping' => ['Restrict skipping', 'Prevent skipping ahead in videos', false],
            'restrict_seeking' => ['Restrict seeking', 'Disable seek bar on video lessons', false],
            'resume_last_position' => ['Resume last position', 'Continue from where the learner left off', true],
            'downloadable_resources' => ['Downloadable resources', 'Allow resource downloads', true],
            'lesson_comments' => ['Lesson comments', 'Enable comments on lessons', false],
            'learner_notes' => ['Learner notes', 'Allow personal lesson notes', true],
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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Min video watch %</label>
            <input type="number" name="min_video_watch_percent" min="0" max="100"
                   value="{{ old('min_video_watch_percent', $config['min_video_watch_percent'] ?? 80) }}"
                   class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
            @error('min_video_watch_percent')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Max completion days</label>
            <input type="number" name="max_completion_days" min="1"
                   value="{{ old('max_completion_days', $config['max_completion_days'] ?? '') }}"
                   placeholder="Optional"
                   class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
            @error('max_completion_days')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Failed attempt limit</label>
            <input type="number" name="failed_attempt_limit" min="1"
                   value="{{ old('failed_attempt_limit', $config['failed_attempt_limit'] ?? '') }}"
                   placeholder="Optional"
                   class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
            @error('failed_attempt_limit')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
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
