@props([
    'icon',
    'class' => 'w-[18px] h-[18px]',
    'strokeWidth' => '1.75',
])

@php
    $paths = is_array($icon)
        ? $icon
        : array_values(array_filter(explode('|', (string) $icon)));
@endphp

<svg {{ $attributes->merge(['class' => $class]) }} fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
    @foreach($paths as $pathD)
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="{{ trim($pathD) }}"/>
    @endforeach
</svg>
