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
