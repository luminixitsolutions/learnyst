@php
    $total = max(count($steps), 1);
@endphp
<div class="signup-progress" aria-hidden="true">
    @for($i = 0; $i < $total; $i++)
        <span class="{{ $i < $stepIndex ? 'is-done' : ($i === $stepIndex ? 'is-active' : '') }}"></span>
    @endfor
</div>
