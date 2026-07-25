@extends('auth.signup.layout')

@section('signup_title', 'Institute details')
@section('signup_heading', 'Institute details')
@section('signup_lead', 'Tell us about your institute so we can set up your public profile and panel.')

@section('progress')
@include('auth.signup.partials.progress')
@endsection

@section('signup_body')
<div class="signup-card">
    <h1>Tell us about your institute</h1>
    <p class="sub">We’ll use this to set up your institute profile.</p>

    @if (session('success'))
        <div class="error" style="background:#ecfdf5;border-color:#a7f3d0;color:#065f46;">{{ session('success') }}</div>
    @endif

    @if (! empty($data['auth_provider']) && $data['auth_provider'] === 'google')
        <p class="sub" style="margin-bottom:14px;color:#15803d;">Signed in with Google as <strong>{{ $data['email'] ?? '' }}</strong></p>
    @endif

    @if ($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('signup.company') }}">
        @csrf
        <div class="field">
            <label for="company_name">Institute name*</label>
            <input id="company_name" type="text" name="company_name" value="{{ old('company_name', $data['company_name'] ?? ($data['google_name'] ?? '')) }}" required autofocus>
        </div>
        <div class="field">
            <label for="phone">Phone number*</label>
            <input id="phone" type="tel" name="phone" value="{{ old('phone', $data['phone'] ?? '') }}" required>
        </div>
        <button type="submit" class="primary-btn">CONTINUE</button>
    </form>

    <div class="footer-link"><a href="{{ route('signup.show', 'account') }}">← Back</a></div>
</div>
@endsection
