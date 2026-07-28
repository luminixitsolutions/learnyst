@extends('website.layouts.app')

@section('title', 'Student Login – ' . config('website.brand'))
@section('meta_description', 'Sign in to your StudyNest student account to access courses, progress, and certificates.')

@section('content')
@php $brand = config('website.brand'); @endphp

<section class="ly-auth-hero">
    <div class="ly-container ly-auth-hero-inner">
        <h1>Student Login</h1>
        <p class="ly-product-lead">Access your courses, track progress, and view certificates from your student panel.</p>
    </div>
</section>

<section class="ly-section ly-section-soft ly-auth-section">
    <div class="ly-container">
        <div class="ly-auth-layout">
            <div class="ly-auth-copy">
                <p class="ly-tag">Welcome back</p>
                <h2>Continue your learning journey</h2>
                <p>Sign in with your student account to open your personalized dashboard, enrolled courses, and certificates.</p>
                <ul class="ly-checklist">
                    <li>Access enrolled courses anytime</li>
                    <li>Track your learning progress</li>
                    <li>Download earned certificates</li>
                    <li>Join academy communities</li>
                </ul>
            </div>

            <div class="ly-auth-card">
                <h3>Sign in</h3>
                <p class="ly-auth-card-lead">Enter your student credentials to continue.</p>

                @if($errors->any())
                    <div class="ly-auth-error">{{ $errors->first() }}</div>
                @endif

                <div class="ly-auth-links" style="margin-bottom:1rem;display:flex;flex-wrap:wrap;gap:.5rem;">
                    <a href="{{ route('auth.social.redirect', 'facebook') }}" class="ly-btn" style="font-size:.8rem;">Facebook</a>
                    <a href="{{ route('auth.social.redirect', 'linkedin') }}" class="ly-btn" style="font-size:.8rem;">LinkedIn</a>
                    <a href="{{ route('auth.social.redirect', 'apple') }}" class="ly-btn" style="font-size:.8rem;">Apple</a>
                </div>
                <div class="ly-auth-or">OR</div>

                <form method="POST" action="{{ route('student.login.submit') }}" class="ly-auth-form">
                    @csrf
                    @if(!empty($redirect))
                        <input type="hidden" name="redirect" value="{{ $redirect }}">
                    @endif
                    <label>
                        <span>Email</span>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="you@example.com">
                    </label>
                    <label>
                        <span>Password</span>
                        <input type="password" name="password" required placeholder="••••••••">
                    </label>
                    <div class="ly-auth-row">
                        <label class="ly-auth-check">
                            <input type="checkbox" name="remember" value="1">
                            <span>Remember me</span>
                        </label>
                        <a href="{{ route('password.request') }}">Forgot password?</a>
                    </div>
                    <button type="submit" class="ly-btn ly-btn-green ly-auth-submit">Sign in as Student</button>
                </form>

                <div class="ly-auth-links">
                    <p>New student? <a href="{{ route('student.register', array_filter(['redirect' => $redirect ?? null])) }}">Create account</a></p>
                    <p class="ly-auth-muted">Institute admin? <a href="{{ route('login') }}">Institute login</a></p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
