@php
    $config = $settings->review_config ?? [];
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
            <p class="text-sm font-medium text-slate-800">Enable ratings & reviews</p>
            <p class="text-xs text-slate-500">Allow learners to rate and review this course</p>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" name="reviews_enabled" value="1" class="sr-only peer" @checked(old('reviews_enabled', $settings->reviews_enabled))>
            <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-slate-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
        </label>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        @foreach([
            'written_reviews' => ['Written reviews', 'Allow text reviews with ratings', true],
            'enrolled_only' => ['Enrolled learners only', 'Only enrolled learners can review', true],
            'allow_anonymous' => ['Allow anonymous', 'Hide reviewer name publicly', false],
            'require_moderation' => ['Require moderation', 'Reviews need approval before publish', true],
            'allow_edit' => ['Allow edit', 'Learners can edit their review', false],
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
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Min completion %</label>
            <input type="number" name="min_completion_percent" min="0" max="100"
                   value="{{ old('min_completion_percent', $config['min_completion_percent'] ?? 0) }}"
                   class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
            @error('min_completion_percent')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Min rating</label>
            <input type="number" name="min_rating" min="1" max="5"
                   value="{{ old('min_rating', $config['min_rating'] ?? 1) }}"
                   class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
            @error('min_rating')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Max rating</label>
            <input type="number" name="max_rating" min="1" max="5"
                   value="{{ old('max_rating', $config['max_rating'] ?? 5) }}"
                   class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
            @error('max_rating')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
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

<div class="mt-10">
    <h2 class="text-sm font-bold text-slate-900 mb-3">Recent reviews</h2>
    <div class="overflow-x-auto rounded-xl border border-slate-200">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Rating</th>
                    <th class="px-4 py-3">Learner</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Submitted</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($reviews as $review)
                    <tr>
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $review->rating }}/5</td>
                        <td class="px-4 py-3 text-slate-700">{{ $review->user?->name ?? 'Unknown' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">{{ $review->status ?? 'pending' }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-500">{{ optional($review->created_at)->format('M j, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-slate-500">No reviews yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($reviews, 'links'))
        <div class="mt-4">{{ $reviews->links() }}</div>
    @endif
</div>
