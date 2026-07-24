@extends('auth.signup.layout')

@section('signup_title', $question['title'] ?? 'Signup')
@section('signup_heading', 'Personalise your setup')
@section('signup_lead', 'A few quick questions help us configure Learnyst for your institute.')

@section('progress')
@include('auth.signup.partials.progress')
@endsection

@section('signup_body')
@php
    $field = $question['field'];
    $choices = $options[$question['option_key']];
@endphp
<div class="signup-card signup-wide">
    <a class="back-link" href="{{ route('signup.show', $question['back']) }}">← Back</a>

    <h2 class="question-title">{{ $question['title'] }} <span style="color:#94a3b8">*</span></h2>
    @if(!empty($question['subtitle']))
        <p class="question-sub">{{ $question['subtitle'] }}</p>
    @endif
    @if(!empty($question['label']))
        <p style="font-weight:600;margin:0 0 14px;">{{ $question['label'] }} <span style="color:#94a3b8">*</span></p>
    @endif

    @if ($errors->any())
        <div class="error" style="max-width:640px;">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ $question['action'] }}" id="choice-form">
        @csrf
        <input type="hidden" name="{{ $field }}" id="choice-value" value="{{ old($field, $data[$field] ?? '') }}">

        <div class="option-list {{ !empty($question['two_col']) ? 'two-col' : '' }}">
            @php $letters = range('A', 'Z'); @endphp
            @foreach($choices as $value => $label)
                <button type="button"
                    class="option-item {{ old($field, $data[$field] ?? '') === $value ? 'is-selected' : '' }}"
                    data-value="{{ $value }}">
                    <span class="letter">{{ $letters[$loop->index] }}</span>
                    <span>{{ $label }}</span>
                </button>
            @endforeach
        </div>

        <button type="submit" class="next-btn">{{ $question['button'] }}</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var form = document.getElementById('choice-form');
    var input = document.getElementById('choice-value');
    document.querySelectorAll('.option-item').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.option-item').forEach(function (el) { el.classList.remove('is-selected'); });
            btn.classList.add('is-selected');
            input.value = btn.getAttribute('data-value');
        });
    });
    form.addEventListener('submit', function (e) {
        if (!input.value) {
            e.preventDefault();
            alert('Please select one of these options.');
        }
    });
})();
</script>
@endpush
