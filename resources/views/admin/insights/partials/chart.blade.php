@props(['chartData', 'emptyMessage' => 'No results found'])

<div class="glass-card rounded-2xl p-6">
    @if($chartData->count())
    <div class="flex items-end gap-2 h-56">
        @php $max = max($chartData->max('value') ?: 1, 1); @endphp
        @foreach($chartData as $point)
        <div class="flex-1 flex flex-col items-center gap-2 min-w-0">
            <div class="w-full rounded-t-lg transition-all"
                 style="height: {{ max(($point->value / $max) * 100, 4) }}%; background: var(--theme-gradient, linear-gradient(to top, #0b7970, #7ac4be)); min-height: 4px;"
                 title="{{ $point->label }}: {{ is_numeric($point->value) ? number_format($point->value, 0) : $point->value }}"></div>
            <span class="text-[9px] text-slate-500 truncate w-full text-center">{{ Str::limit($point->label, 8) }}</span>
        </div>
        @endforeach
    </div>
    @else
    <x-empty-state :title="$emptyMessage" />
    @endif
</div>
