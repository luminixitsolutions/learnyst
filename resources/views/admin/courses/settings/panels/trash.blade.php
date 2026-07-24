@php
    $blockers = $blockers ?? session('trash_blockers', []);
    $canTrash = empty($blockers);
@endphp

<div>
    <h1 class="text-2xl font-bold text-slate-900">{{ $meta['title'] }}</h1>
    <p class="text-sm text-slate-500 mt-1">{{ $meta['description'] }}</p>
</div>

<div class="mt-6 space-y-3">
    <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
        Moving this course to trash soft-deletes it. It can be restored within
        <strong>{{ $retentionDays }}</strong> days, after which permanent cleanup may apply.
    </div>
    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
        Learners will lose access. Bundles, enrollments, and related settings stay linked for restore.
    </div>
</div>

@if(!empty($blockers))
    <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3">
        <p class="text-sm font-semibold text-rose-800 mb-2">Cannot move to trash while these blockers exist:</p>
        <ul class="list-disc list-inside text-sm text-rose-700 space-y-1">
            @foreach($blockers as $blocker)
                <li>{{ $blocker }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.courses.settings.trash', $course) }}" class="mt-8 space-y-5"
      x-data="{ nameInput: '', expected: @js($course->title) }"
      @change="markDirty()" x-on:input="markDirty()">
    @csrf

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">
            Type the course name to confirm: <span class="font-semibold text-slate-900">{{ $course->title }}</span>
        </label>
        <input type="text" name="course_name" x-model="nameInput" required autocomplete="off"
               class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
        @error('course_name')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Reason (optional)</label>
        <textarea name="reason" rows="3" maxlength="1000"
                  class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">{{ old('reason') }}</textarea>
        @error('reason')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <label class="flex items-center gap-2 text-sm text-slate-700">
        <input type="checkbox" name="confirm" value="1" required class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
               @disabled(! $canTrash)>
        I understand this course will be moved to trash
    </label>
    @error('confirm')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror

    <div class="sticky bottom-0 -mx-6 md:-mx-8 mt-10 px-6 md:px-8 py-4 border-t border-slate-200 bg-white flex items-center justify-end gap-3 rounded-b-2xl">
        <a href="{{ route('admin.courses.settings.hub', $course) }}"
           class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Keep Editing</a>
        <button type="submit"
                :disabled="!{{ $canTrash ? 'true' : 'false' }} || nameInput !== expected"
                class="px-5 py-2.5 rounded-xl text-sm font-semibold transition disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400 bg-slate-900 text-white hover:bg-slate-800">
            Move to Trash
        </button>
    </div>
</form>
