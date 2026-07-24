@extends('auth.signup.layout')

@section('signup_title', 'Verify your email')
@section('signup_heading', 'Verify your email')
@section('signup_lead', 'Activate your institute account to open the panel and finish setup.')

@section('signup_body')
@php $email = session('signup_verify_email'); @endphp
<div class="signup-card">
    <h1 style="text-align:left;">Verify your email</h1>
    <p class="sub" style="text-align:left;">
        We have sent a message to <strong>{{ $email }}</strong> with a link to activate your account.
    </p>

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    <div class="verify-actions">
        <a class="outline-btn" href="https://mail.google.com" target="_blank" rel="noopener">Open Gmail</a>
        <a class="outline-btn" href="https://outlook.live.com" target="_blank" rel="noopener">Open Outlook</a>
    </div>

    <p style="text-align:center;color:#64748b;font-size:13px;margin:0 0 10px;">Didn't get the verification email? Check spam!</p>
    <p class="retry" id="retry-text">Retry in <span id="retry-count">01:30</span></p>

    <form method="POST" action="{{ route('signup.resend') }}" id="resend-form" style="display:none;margin-top:10px;">
        @csrf
        <button type="submit" class="primary-btn">Resend verification email</button>
    </form>

    <form method="POST" action="{{ route('signup.verified') }}" style="margin-top:16px;">
        @csrf
        <button type="submit" class="primary-btn">I've verified — Continue to dashboard</button>
    </form>

    <div class="footer-link">If you've already verified the email. <a href="{{ route('login') }}">Institute login</a></div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var seconds = 90;
    var text = document.getElementById('retry-text');
    var form = document.getElementById('resend-form');
    var count = document.getElementById('retry-count');
    var timer = setInterval(function () {
        seconds -= 1;
        if (seconds <= 0) {
            clearInterval(timer);
            text.style.display = 'none';
            form.style.display = 'block';
            return;
        }
        var m = String(Math.floor(seconds / 60)).padStart(2, '0');
        var s = String(seconds % 60).padStart(2, '0');
        count.textContent = m + ':' + s;
    }, 1000);
})();
</script>
@endpush
