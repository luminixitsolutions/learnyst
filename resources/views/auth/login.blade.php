@extends('website.layouts.app')

@php
    $isPlatform = ($panel ?? 'company') === 'platform';
    $brand = config('website.brand');
@endphp

@section('title', ($isPlatform ? 'Platform Admin Login' : 'Institute Login') . ' – ' . $brand)
@section('meta_description', $isPlatform
    ? 'Sign in to the Learnyst platform admin panel.'
    : 'Sign in to your Learnyst institute panel to manage courses, learners, and academy settings.')

@section('content')
<section class="ly-auth-hero {{ $isPlatform ? 'ly-auth-hero-platform' : '' }}">
    <div class="ly-container ly-auth-hero-inner">
        <h1>{{ $isPlatform ? 'Platform Admin Login' : 'Institute Login' }}</h1>
        <p class="ly-product-lead">
            {{ $isPlatform
                ? 'Sign in to manage the Learnyst platform, institutes, and global settings.'
                : 'Sign in to manage your institute — courses, learners, sales, and public profile.' }}
        </p>
    </div>
</section>

<section class="ly-section ly-section-soft ly-auth-section">
    <div class="ly-container">
        <div class="ly-auth-layout">
            <div class="ly-auth-copy">
                <p class="ly-tag">{{ $isPlatform ? 'Platform access' : 'For institutes & creators' }}</p>
                <h2>{{ $isPlatform ? 'Manage the Learnyst platform' : 'Grow your learning business' }}</h2>
                <p>
                    {{ $isPlatform
                        ? 'Access platform tools for institutes, users, website content, and system settings.'
                        : 'Open your institute panel to publish courses, track enrollments, handle payments, and update your public institute profile.' }}
                </p>
                <ul class="ly-checklist">
                    @if($isPlatform)
                        <li>Manage registered institutes</li>
                        <li>Control platform website content</li>
                        <li>Review users and activity logs</li>
                        <li>Configure platform settings</li>
                    @else
                        <li>Build and publish courses</li>
                        <li>Manage learners and enrollments</li>
                        <li>Track sales and insights</li>
                        <li>Update your public institute page</li>
                    @endif
                </ul>
            </div>

            <div class="ly-auth-card">
                <h3>Sign in</h3>
                <p class="ly-auth-card-lead">
                    {{ $isPlatform ? 'Enter your platform admin credentials.' : 'Enter your institute admin credentials.' }}
                </p>

                @if($errors->any())
                    <div class="ly-auth-error">{{ $errors->first() }}</div>
                @endif

                @if(session('success'))
                    <div class="ly-auth-error" style="background:#ecfdf5;border-color:#a7f3d0;color:#065f46;">{{ session('success') }}</div>
                @endif

                @if(! $isPlatform && app(\App\Services\GoogleOAuthService::class)->isEnabled())
                    <a href="{{ route('auth.google.redirect', ['intent' => 'login']) }}" class="ly-google-btn">
                        <svg width="18" height="18" viewBox="0 0 48 48" aria-hidden="true"><path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.7 32.7 29.3 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3 0 5.8 1.1 7.9 3l5.7-5.7C34.2 6.1 29.4 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.2-.1-2.3-.4-3.5z"/><path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.6 16 19 12 24 12c3 0 5.8 1.1 7.9 3l5.7-5.7C34.2 6.1 29.4 4 24 4 16.1 4 9.3 8.5 6.3 14.7z"/><path fill="#4CAF50" d="M24 44c5.2 0 9.9-2 13.4-5.2l-6.2-5.2C29.2 35.3 26.7 36 24 36c-5.3 0-9.7-3.3-11.3-8l-6.5 5C9.2 39.5 16 44 24 44z"/><path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-1.1 3.1-3.3 5.5-5.9 7.1l.1.1 6.2 5.2C37.3 39.1 44 34 44 24c0-1.2-.1-2.3-.4-3.5z"/></svg>
                        Continue with Google
                    </a>
                    <div class="ly-auth-or">OR</div>
                @endif

                <form method="POST" action="{{ $isPlatform ? route('platform.login.submit') : route('login') }}" class="ly-auth-form">
                    @csrf
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
                    <button type="submit" class="ly-btn ly-btn-green ly-auth-submit">
                        {{ $isPlatform ? 'Sign in to Platform' : 'Sign in to Institute Panel' }}
                    </button>
                </form>

                <div class="ly-auth-links">
                    @if($isPlatform)
                        <p>Institute admin? <a href="{{ route('login') }}">Institute login</a></p>
                        <p class="ly-auth-muted">Student? <a href="{{ route('student.login') }}">Student login</a></p>
                    @else
                        <p>New institute? <a href="{{ route('signup.show') }}">Start free trial</a></p>
                        <p class="ly-auth-muted">Student? <a href="{{ route('student.login') }}">Student login</a></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
