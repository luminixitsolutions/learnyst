@php
    $layout = $layout ?? [];
    $html = $html ?? '';
    $replacements = $replacements ?? [];
    $preview = $preview ?? false;
    $theme = $layout['theme'] ?? 'classic-blue-gold';
    $orientation = $layout['orientation'] ?? 'A4-landscape';
    $themeClass = match ($theme) {
        'emerald' => 'cert-theme-emerald',
        'minimal' => 'cert-theme-minimal',
        default => '',
    };
    $orientationClass = $orientation === 'A4-portrait' ? 'cert-portrait' : '';
    $rendered = $html;
    if ($preview) {
        foreach ($replacements as $key => $value) {
            $rendered = str_replace(
                ['{'.$key.'}', '{{'.$key.'}}'],
                '<span class="cert-placeholder">{'.$key.'}</span>',
                $rendered
            );
        }
    } else {
        foreach ($replacements as $key => $value) {
            $rendered = str_replace(['{'.$key.'}', '{{'.$key.'}}'], e((string) $value), $rendered);
        }
    }
@endphp

<div class="cert-sheet {{ $themeClass }} {{ $orientationClass }}"
     style="--cert-primary: {{ $layout['primary_color'] ?? '#1e4a8c' }}; --cert-accent: {{ $layout['accent_color'] ?? '#c9a227' }}; --cert-paper: {{ $layout['paper_color'] ?? '#fffef8' }};">
    {!! $rendered !!}
</div>
