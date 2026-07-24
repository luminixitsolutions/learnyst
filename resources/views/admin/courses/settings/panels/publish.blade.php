@php
    $missing = $missing ?? session('publish_missing', []);
    $currentStatus = $course->status ?? 'draft';
@endphp

<div>
    <h1 class="text-2xl font-bold text-slate-900">{{ $meta['title'] }}</h1>
    <p class="text-sm text-slate-500 mt-1">{{ $meta['description'] }}</p>
</div>

<div class="mt-6 flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
    <span class="text-sm text-slate-600">Current status</span>
    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold
        {{ $currentStatus === 'published' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-200 text-slate-700' }}">
        {{ ucfirst($currentStatus) }}
    </span>
    @if($settings->published_at)
        <span class="text-xs text-slate-500">Published {{ $settings->published_at->format('M j, Y g:i A') }}</span>
    @endif
</div>

@if(!empty($missing))
    <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3">
        <p class="text-sm font-semibold text-rose-800 mb-2">Complete these requirements before publishing:</p>
        <ul class="list-disc list-inside text-sm text-rose-700 space-y-1">
            @foreach($missing as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.courses.settings.publish', $course) }}" class="mt-8 space-y-6"
      x-data="{ status: @js(old('status', $currentStatus === 'published' ? 'published' : 'unpublished')), showConfirm: false }"
      @change="markDirty()" x-on:input="markDirty()"
      @submit.prevent="showConfirm = true">
    @csrf

    <div class="space-y-3">
        <label class="flex items-start gap-3 rounded-xl border border-slate-200 px-4 py-4 cursor-pointer has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-50/40">
            <input type="radio" name="status" value="published" x-model="status" class="mt-1 text-emerald-600 focus:ring-emerald-500"
                   @disabled(!empty($missing))>
            <div>
                <p class="text-sm font-semibold text-slate-900">Published</p>
                <p class="text-xs text-slate-500 mt-1">Course is live and available according to your visibility and pricing settings.</p>
            </div>
        </label>
        <label class="flex items-start gap-3 rounded-xl border border-slate-200 px-4 py-4 cursor-pointer has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-50/40">
            <input type="radio" name="status" value="unpublished" x-model="status" class="mt-1 text-emerald-600 focus:ring-emerald-500">
            <div>
                <p class="text-sm font-semibold text-slate-900">Unpublished</p>
                <p class="text-xs text-slate-500 mt-1">Hide the course from new learners. Existing enrollments keep access.</p>
            </div>
        </label>
        <label class="flex items-start gap-3 rounded-xl border border-slate-200 px-4 py-4 cursor-pointer has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-50/40">
            <input type="radio" name="status" value="draft" x-model="status" class="mt-1 text-emerald-600 focus:ring-emerald-500">
            <div>
                <p class="text-sm font-semibold text-slate-900">Draft</p>
                <p class="text-xs text-slate-500 mt-1">Keep the course as a work-in-progress. Not visible to learners.</p>
            </div>
        </label>
        @error('status')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Notes (optional)</label>
        <textarea name="notes" rows="3" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">{{ old('notes') }}</textarea>
    </div>

    <label class="flex items-center gap-2 text-sm text-slate-700">
        <input type="checkbox" name="confirm" value="1" required class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
        I confirm I want to update this course status
    </label>
    @error('confirm')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror

    <div class="sticky bottom-0 -mx-6 md:-mx-8 mt-10 px-6 md:px-8 py-4 border-t border-slate-200 bg-white flex items-center justify-end gap-3 rounded-b-2xl">
        <a href="{{ route('admin.courses.settings.hub', $course) }}"
           @click.prevent="requestLeave('{{ route('admin.courses.settings.hub', $course) }}')"
           class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</a>
        <button type="submit" :disabled="!dirty"
                class="px-5 py-2.5 rounded-xl text-sm font-semibold transition disabled:cursor-not-allowed"
                :class="dirty ? 'bg-slate-900 text-white hover:bg-slate-800' : 'bg-slate-100 text-slate-400'">Update status</button>
    </div>

    <div x-show="showConfirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6" @click.outside="showConfirm = false">
            <h3 class="text-lg font-bold text-slate-900 mb-2">Confirm status change</h3>
            <p class="text-sm text-slate-500 mb-6">
                Set course status to <span class="font-semibold text-slate-800" x-text="status"></span>?
            </p>
            <div class="flex justify-end gap-3">
                <button type="button" @click="showConfirm = false" class="px-4 py-2 rounded-xl border border-slate-200 text-sm">Cancel</button>
                <button type="button" @click="$el.closest('form').submit()" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm">Confirm</button>
            </div>
        </div>
    </div>
</form>

<div class="mt-10">
    <h2 class="text-sm font-bold text-slate-900 mb-3">Publication history</h2>
    @if($history->isEmpty())
        <p class="text-sm text-slate-500">No status changes recorded yet.</p>
    @else
        <ol class="relative border-l border-slate-200 ml-3 space-y-4">
            @foreach($history as $entry)
                <li class="ml-4">
                    <div class="absolute -left-1.5 mt-1.5 w-3 h-3 rounded-full bg-emerald-500 border-2 border-white"></div>
                    <p class="text-sm font-medium text-slate-800">
                        {{ $entry->from_status ?? '—' }} → {{ $entry->to_status }}
                    </p>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ optional($entry->changedBy)->name ?? 'System' }}
                        · {{ optional($entry->created_at)->format('M j, Y g:i A') }}
                    </p>
                    @if($entry->notes)
                        <p class="text-xs text-slate-600 mt-1">{{ $entry->notes }}</p>
                    @endif
                </li>
            @endforeach
        </ol>
    @endif
</div>
