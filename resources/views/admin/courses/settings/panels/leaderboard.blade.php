@php
    $config = $settings->leaderboard_config ?? [];
    $rankingBy = old('ranking_by', $config['ranking_by'] ?? ['completion']);
@endphp

<div>
    <h1 class="text-2xl font-bold text-slate-900">{{ $meta['title'] }}</h1>
    <p class="text-sm text-slate-500 mt-1">{{ $meta['description'] }}</p>
</div>

<form method="POST" action="{{ route('admin.courses.settings.update', [$course, $panel]) }}" class="mt-8 space-y-6" @change="markDirty()" x-on:input="markDirty()">
    @csrf
    @method('PUT')

    <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
        <div>
            <p class="text-sm font-medium text-slate-800">Enable leaderboard</p>
            <p class="text-xs text-slate-500">Show ranked learner progress for this course</p>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" name="leaderboard_enabled" value="1" class="sr-only peer" @checked(old('leaderboard_enabled', $settings->leaderboard_enabled))>
            <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-slate-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
        </label>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-2">Rank learners by</label>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            @foreach([
                'completion' => 'Completion',
                'quiz' => 'Quiz scores',
                'assignment' => 'Assignments',
                'time' => 'Time spent',
                'points' => 'Points',
                'certificate' => 'Certificate',
            ] as $value => $label)
                <label class="flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2.5 text-sm has-[:checked]:border-emerald-300 has-[:checked]:bg-emerald-50/50">
                    <input type="checkbox" name="ranking_by[]" value="{{ $value }}"
                           class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                           @checked(in_array($value, (array) $rankingBy, true))>
                    {{ $label }}
                </label>
            @endforeach
        </div>
        @error('ranking_by')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Period</label>
            <select name="period" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
                @foreach(['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'all_time' => 'All time'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('period', $config['period'] ?? 'all_time') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('period')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
            <div>
                <p class="text-sm font-medium text-slate-800">Privacy mode</p>
                <p class="text-xs text-slate-500">Mask learner names on the leaderboard</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="privacy_mode" value="1" class="sr-only peer" @checked(old('privacy_mode', $config['privacy_mode'] ?? false))>
                <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-slate-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
            </label>
        </div>
        <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
            <div>
                <p class="text-sm font-medium text-slate-800">Course only</p>
                <p class="text-xs text-slate-500">Limit rankings to this course</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="course_only" value="1" class="sr-only peer" @checked(old('course_only', $config['course_only'] ?? true))>
                <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-slate-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
            </label>
        </div>
        <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 md:col-span-2">
            <div>
                <p class="text-sm font-medium text-slate-800">Assignment-based ranking</p>
                <p class="text-xs text-slate-500">Prioritize assignment scores in ranking calculations</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="assignment_based" value="1" class="sr-only peer" @checked(old('assignment_based', $config['assignment_based'] ?? false))>
                <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-slate-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
            </label>
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
