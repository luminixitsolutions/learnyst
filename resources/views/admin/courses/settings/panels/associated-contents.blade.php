<div>
    <h1 class="text-2xl font-bold text-slate-900">{{ $meta['title'] }}</h1>
    <p class="text-sm text-slate-500 mt-1">{{ $meta['description'] }}</p>
</div>

<div class="mt-8 space-y-3">
    @forelse($associations as $item)
        <a href="{{ $item['url'] }}"
           class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 px-5 py-4 hover:border-emerald-300 hover:bg-emerald-50/30 transition group">
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <h2 class="text-sm font-semibold text-slate-900">{{ $item['title'] }}</h2>
                    <span class="text-[11px] font-medium px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">{{ $item['count'] }}</span>
                </div>
                <p class="text-sm text-slate-500 mt-1">{{ $item['description'] }}</p>
            </div>
            <svg class="w-5 h-5 text-slate-400 group-hover:text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    @empty
        <div class="rounded-xl border border-dashed border-slate-200 p-10 text-center">
            <p class="text-sm font-medium text-slate-700">No associations found</p>
            <p class="text-sm text-slate-500 mt-1">Related products, bundles, and categories will appear here.</p>
        </div>
    @endforelse
</div>

<div class="mt-8 flex justify-end">
    <a href="{{ route('admin.courses.settings.hub', $course) }}"
       class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Back to settings</a>
</div>
