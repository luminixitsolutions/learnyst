@props(['title', 'value', 'icon' => null, 'trend' => null, 'color' => 'brand', 'href' => null])

@php
    $cardClass = 'glass-card rounded-2xl p-5 transition-all duration-200 block';
    $cardClass .= $href ? ' hover:shadow-lg hover:border-teal-200 cursor-pointer group' : ' hover:shadow-lg';
@endphp

@if($href)
<a href="{{ $href }}" class="{{ $cardClass }}">
@else
<div class="{{ $cardClass }}">
@endif
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm text-slate-500 font-medium group-hover:text-teal-600 transition-colors">{{ $title }}</p>
            <p class="text-2xl font-bold text-slate-800 mt-1 group-hover:text-teal-700 transition-colors">{{ $value }}</p>
            @if($trend)<p class="text-xs text-teal-600 mt-1 font-medium">{{ $trend }}</p>@endif
        </div>
        @if($icon)
            <div class="w-11 h-11 rounded-xl bg-teal-50 flex items-center justify-center text-teal-600 group-hover:bg-teal-100 transition-colors">
                {!! $icon !!}
            </div>
        @elseif($href)
            <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-teal-50 group-hover:text-teal-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
        @endif
    </div>
@if($href)
</a>
@else
</div>
@endif
