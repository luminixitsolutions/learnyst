@props(['current' => 1])

@php
$steps = [
    1 => 'Details',
    2 => 'Role',
    3 => 'Courses',
    4 => 'Bundles',
    5 => 'Communities',
    6 => 'Preview',
];
@endphp

<div class="glass-card rounded-2xl p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-slate-500">Step {{ $current }} of 6 — {{ $steps[$current] ?? '' }}</p>
        <p class="text-sm font-medium text-indigo-600">{{ round(($current / 6) * 100) }}% complete</p>
    </div>
    <div class="h-2 rounded-full bg-slate-800 overflow-hidden mb-6">
        <div class="h-full bg-brand-500 rounded-full transition-all duration-300" style="width: {{ ($current / 6) * 100 }}%"></div>
    </div>
    <div class="flex justify-between">
        @foreach($steps as $num => $label)
        <div class="flex flex-col items-center flex-1">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2 transition
                {{ $num < $current ? 'bg-brand-500 border-indigo-400 text-white' : ($num === $current ? 'border-indigo-400 text-indigo-600 bg-indigo-50' : 'border-slate-200 text-slate-500') }}">
                @if($num < $current)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                @else
                    {{ $num }}
                @endif
            </div>
            <span class="text-xs mt-2 hidden sm:block {{ $num === $current ? 'text-indigo-600' : 'text-slate-500' }}">{{ $label }}</span>
        </div>
        @if(!$loop->last)
        <div class="flex-1 h-0.5 mt-4 mx-1 {{ $num < $current ? 'bg-brand-500' : 'bg-slate-800' }}"></div>
        @endif
        @endforeach
    </div>
</div>
