@props([
    'backRoute' => null,
    'backLabel' => 'Back',
    'searchPlaceholder' => null,
    'showDateRange' => false,
    'showPeriod' => false,
    'showReset' => false,
    'showExport' => true,
    'showColumns' => true,
    'showInfo' => false,
    'infoText' => null,
    'from' => null,
    'to' => null,
    'filters' => null,
])

<div class="space-y-4" x-data="{ showColumns: false, loading: false }" @submit="loading = true">
    <form method="GET" class="glass-card rounded-2xl p-4 space-y-4" @reset="loading = false">
        <div class="flex flex-wrap items-center gap-3">
            @if($backRoute)
            <a href="{{ $backRoute }}" class="px-3 py-2 rounded-xl border border-slate-200 text-sm text-slate-600 hover:bg-slate-50">{{ $backLabel }}</a>
            @endif

            @if($searchPlaceholder)
            <div class="flex-1 min-w-[200px]">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="{{ $searchPlaceholder }}"
                    class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-800 text-sm shadow-sm">
            </div>
            @endif

            @if($showDateRange)
            <input type="date" name="from" value="{{ $from ?? request('from', now()->startOfMonth()->format('Y-m-d')) }}"
                class="px-3 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-800 text-sm">
            <span class="text-slate-400 text-sm">to</span>
            <input type="date" name="to" value="{{ $to ?? request('to', now()->format('Y-m-d')) }}"
                class="px-3 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-800 text-sm">
            @endif

            @if($showPeriod)
            <select name="period" class="px-3 py-2.5 rounded-xl border border-slate-200 text-slate-800 text-sm">
                @foreach(['days' => 'Days', 'weeks' => 'Weeks', 'months' => 'Months'] as $val => $label)
                    <option value="{{ $val }}" @selected(request('period', 'days') === $val)>{{ $label }}</option>
                @endforeach
            </select>
            @endif

            {{ $filters ?? '' }}

            @if($showColumns)
            <button type="button" @click="showColumns = !showColumns" class="panel-btn-secondary text-sm">Columns</button>
            @endif

            <button type="submit" class="panel-btn-primary text-sm">Apply</button>

            @if($showReset)
            <a href="{{ url()->current() }}" class="panel-btn-secondary text-sm">Reset</a>
            @endif

            @if($showExport)
            <a href="{{ request()->fullUrlWithQuery(array_merge(request()->query(), ['export' => 1])) }}"
                class="px-4 py-2.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 text-sm font-medium hover:bg-emerald-100">
                Export CSV
            </a>
            @endif

            @if($showInfo && $infoText)
            <span class="text-slate-400 cursor-help" title="{{ $infoText }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            @endif
        </div>

        @if($showColumns)
        <div x-show="showColumns" x-cloak class="pt-3 border-t border-slate-100">
            <div class="flex flex-wrap gap-4 text-sm text-slate-600">{{ $slot }}</div>
        </div>
        @endif
    </form>

    <div x-show="loading" x-cloak class="flex items-center gap-2 text-sm text-slate-500">
        <svg class="animate-spin h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
        Loading...
    </div>
</div>
