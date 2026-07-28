@props([
    'height' => 40,
    'class' => '',
    'link' => null,
    'alt' => null,
])

@php
    $logoUrl = \App\Support\Brand::logoUrl();
    $brandName = $alt ?? \App\Support\Brand::name();
    $heightCss = is_numeric($height) ? $height.'px' : $height;
@endphp

@if($link === false)
    <img src="{{ $logoUrl }}" alt="{{ $brandName }}" {{ $attributes->class('brand-logo-img '.$class)->merge([
        'style' => 'height: '.$heightCss.'; width: auto; max-width: 100%; object-fit: contain; display: block;',
    ]) }}>
@else
    <a href="{{ $link ?? url('/') }}" {{ $attributes->class('brand-logo-link inline-flex items-center shrink-0') }} aria-label="{{ $brandName }}">
        <img src="{{ $logoUrl }}" alt="{{ $brandName }}" class="brand-logo-img {{ $class }}"
             style="height: {{ $heightCss }}; width: auto; max-width: 100%; object-fit: contain; display: block;">
    </a>
@endif
