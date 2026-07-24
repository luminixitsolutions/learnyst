@extends('auth.signup.layout')

@section('signup_title', 'Start your free trial')
@section('signup_heading', 'Institute Register')
@section('signup_lead', 'No credit card required. Set up your institute and start selling courses in minutes.')

@section('signup_body')
<div class="signup-card">
    <h1>Start your free trial</h1>
    <p class="sub">Create your institute admin account</p>

    @if ($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif

    <button type="button" class="google-btn" onclick="alert('Google signup can be connected later.')">
        <svg width="18" height="18" viewBox="0 0 48 48"><path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.7 32.7 29.3 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3 0 5.8 1.1 7.9 3l5.7-5.7C34.2 6.1 29.4 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.2-.1-2.3-.4-3.5z"/><path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.6 16 19 12 24 12c3 0 5.8 1.1 7.9 3l5.7-5.7C34.2 6.1 29.4 4 24 4 16.1 4 9.3 8.5 6.3 14.7z"/><path fill="#4CAF50" d="M24 44c5.2 0 9.9-2 13.4-5.2l-6.2-5.2C29.2 35.3 26.7 36 24 36c-5.3 0-9.7-3.3-11.3-8l-6.5 5C9.2 39.5 16 44 24 44z"/><path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-1.1 3.1-3.3 5.5-5.9 7.1l.1.1 6.2 5.2C37.3 39.1 44 34 44 24c0-1.2-.1-2.3-.4-3.5z"/></svg>
        Continue with Google
    </button>

    <div class="divider">OR</div>

    <form method="POST" action="{{ route('signup.account') }}" id="account-form">
        @csrf
        <div class="field">
            <label for="email">Email*</label>
            <input id="email" type="email" name="email" value="{{ old('email', $data['email'] ?? '') }}" required autofocus>
        </div>
        <div class="field">
            <label for="password">Password*</label>
            <div class="password-wrap">
                <input id="password" type="password" name="password" required>
                <button type="button" id="toggle-password" aria-label="Show password">👁</button>
            </div>
        </div>

        <button type="submit" class="primary-btn" id="continue-btn" disabled>CONTINUE</button>

        <label class="terms">
            <input type="checkbox" name="terms" value="1" id="terms" {{ old('terms') ? 'checked' : '' }}>
            <span>By signing up, you acknowledge that you have read, understood, and agree to Learnyst's <a href="{{ route('website.page', 'terms-and-conditions') }}" target="_blank">Terms</a> and <a href="{{ route('website.page', 'privacy-policy') }}" target="_blank">Privacy Policy</a></span>
        </label>
    </form>

    <div class="footer-link">Already have an account? <a href="{{ route('login') }}">Institute login</a></div>
    <div class="footer-link" style="margin-top:8px;font-size:13px;">Student? <a href="{{ route('student.register') }}">Student register</a></div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var terms = document.getElementById('terms');
    var btn = document.getElementById('continue-btn');
    var email = document.getElementById('email');
    var password = document.getElementById('password');
    function sync() {
        btn.disabled = !(terms.checked && email.value.trim() && password.value.trim());
    }
    ['input', 'change'].forEach(function (evt) {
        terms.addEventListener(evt, sync);
        email.addEventListener(evt, sync);
        password.addEventListener(evt, sync);
    });
    document.getElementById('toggle-password').addEventListener('click', function () {
        password.type = password.type === 'password' ? 'text' : 'password';
    });
    sync();
})();
</script>
@endpush
