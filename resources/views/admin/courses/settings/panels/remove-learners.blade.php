<div>
    <h1 class="text-2xl font-bold text-slate-900">{{ $meta['title'] }}</h1>
    <p class="text-sm text-slate-500 mt-1">{{ $meta['description'] }}</p>
</div>

<form method="GET" action="{{ route('admin.courses.settings.show', [$course, $panel]) }}" class="mt-6">
    <div class="relative max-w-md">
        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Search learners by name, email, or phone"
               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
    </div>
</form>

<form method="POST" action="{{ route('admin.courses.settings.remove-learners', $course) }}" class="mt-6 space-y-6"
      x-data="{
          selected: [],
          allIds: @js($learners->pluck('user_id')->values()),
          toggleAll(checked) { this.selected = checked ? [...this.allIds] : []; }
      }"
      @change="markDirty()" x-on:input="markDirty()">
    @csrf

    <div class="overflow-x-auto rounded-xl border border-slate-200">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3 w-10">
                        <input type="checkbox" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                               @change="toggleAll($event.target.checked); markDirty()">
                    </th>
                    <th class="px-4 py-3">Learner</th>
                    <th class="px-4 py-3">Progress</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Enrolled</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($learners as $enrollment)
                    <tr>
                        <td class="px-4 py-3">
                            <input type="checkbox" name="learner_ids[]" value="{{ $enrollment->user_id }}"
                                   class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                                   x-model.number="selected">
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-slate-900">{{ $enrollment->user?->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-slate-500">{{ $enrollment->user?->email }}</p>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ number_format($enrollment->progress_percent ?? $enrollment->progress ?? 0, 0) }}%</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">{{ $enrollment->status }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-500">{{ optional($enrollment->enrolled_at ?? $enrollment->created_at)->format('M j, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">No learners found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="text-xs text-slate-500"><span x-text="selected.length"></span> learner(s) selected</div>
    @error('learner_ids')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror

    @if(method_exists($learners, 'links'))
        <div>{{ $learners->links() }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Type course name to confirm: <span class="font-semibold">{{ $course->title }}</span>
            </label>
            <input type="text" name="course_name" required value="{{ old('course_name') }}"
                   class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
            @error('course_name')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Reason (optional)</label>
            <input type="text" name="reason" value="{{ old('reason') }}" maxlength="1000"
                   class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
            @error('reason')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <label class="flex items-center gap-2 text-sm text-slate-700">
        <input type="checkbox" name="confirm" value="1" required class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
        I confirm removal of selected learners (payment history is kept)
    </label>
    @error('confirm')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.courses.settings.hub', $course) }}"
           @click.prevent="requestLeave('{{ route('admin.courses.settings.hub', $course) }}')"
           class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 text-white text-sm font-semibold hover:bg-rose-700"
                :disabled="selected.length === 0"
                :class="selected.length === 0 ? 'opacity-50 cursor-not-allowed' : ''">
            Remove selected
        </button>
    </div>
</form>

<div class="mt-10">
    <h2 class="text-sm font-bold text-slate-900 mb-3">Recent removals</h2>
    <div class="space-y-2">
        @forelse($removals as $removal)
            <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 px-4 py-3">
                <div>
                    <p class="text-sm font-medium text-slate-900">{{ $removal->user?->name ?? 'Unknown' }}</p>
                    <p class="text-xs text-slate-500">
                        Removed {{ optional($removal->created_at)->format('M j, Y') }}
                        by {{ $removal->removedBy?->name ?? '—' }}
                        @if($removal->reason) · {{ $removal->reason }} @endif
                    </p>
                </div>
                @if(is_null($removal->restored_at))
                    <form method="POST" action="{{ route('admin.courses.settings.removals.restore', [$course, $removal]) }}">
                        @csrf
                        <button type="submit" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800">Restore</button>
                    </form>
                @else
                    <span class="text-xs text-slate-400">Restored</span>
                @endif
            </div>
        @empty
            <p class="text-sm text-slate-500">No removals recorded yet.</p>
        @endforelse
    </div>
</div>
