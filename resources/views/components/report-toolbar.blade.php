@props([
    'title' => null,
    'searchPlaceholder' => 'Search...',
    'showDateRange' => false,
    'from' => null,
    'to' => null,
    'filters' => null,
])

<div class="space-y-4" x-data="{ showFilters: false, showColumns: false }">
    <form method="GET" class="glass-card rounded-2xl p-4 space-y-4">
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="search" name="search" value="{{ request('search') }}"
                    placeholder="{{ $searchPlaceholder }}"
                    class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-800 text-sm shadow-sm">
            </div>
            @if($showDateRange)
            <input type="date" name="from" value="{{ $from ?? request('from') }}"
                class="px-3 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-800 text-sm">
            <span class="text-slate-400 text-sm">to</span>
            <input type="date" name="to" value="{{ $to ?? request('to') }}"
                class="px-3 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-800 text-sm">
            @endif
            <button type="button" @click="showFilters = !showFilters" class="panel-btn-secondary text-sm">Filters</button>
            <button type="button" @click="showColumns = !showColumns" class="panel-btn-secondary text-sm">Columns</button>
            <button type="submit" class="panel-btn-primary text-sm">Apply</button>
            <a href="{{ request()->fullUrlWithQuery(array_merge(request()->query(), ['export' => 1])) }}"
                class="px-4 py-2.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 text-sm font-medium hover:bg-emerald-100">
                Export CSV
            </a>
            <a href="{{ route('admin.reports.index') }}" class="text-sm text-slate-500 hover:text-indigo-600 ml-auto">← All Reports</a>
        </div>

        <div x-show="showFilters" x-cloak class="pt-3 border-t border-slate-100">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                {{ $filters ?? '' }}
            </div>
        </div>

        <div x-show="showColumns" x-cloak class="pt-3 border-t border-slate-100">
            <p class="text-xs text-slate-500 mb-2">Toggle optional columns (saved for this session in URL params)</p>
            <div class="flex flex-wrap gap-4">
                {{ $slot }}
            </div>
        </div>

        @foreach(request()->except(['page', 'export']) as $key => $value)
            @if(is_array($value))
                @foreach($value as $v)
                    <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                @endforeach
            @elseif(! in_array($key, ['search', 'from', 'to', 'status', 'type', 'gateway', 'product_id', 'learner_id', 'course_id', 'bundle_id']))
                @continue
            @endif
        @endforeach
    </form>
</div>
