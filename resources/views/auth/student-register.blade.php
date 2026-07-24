@extends('website.layouts.app')

@section('title', 'Student Registration – ' . config('website.brand'))
@section('meta_description', 'Create your Learnyst student account to start learning online.')

@section('content')
@php $brand = config('website.brand'); @endphp

<section class="ly-auth-hero">
    <div class="ly-container ly-auth-hero-inner">
        <h1>Student Registration</h1>
        <p class="ly-product-lead">Create your free student account and start learning with academies on {{ $brand }}.</p>
    </div>
</section>

<section class="ly-section ly-section-soft ly-auth-section">
    <div class="ly-container">
        <div class="ly-auth-layout">
            <div class="ly-auth-copy">
                <p class="ly-tag">Get started</p>
                <h2>Join as a student</h2>
                <p>Register once to browse courses, enroll, learn at your pace, and earn certificates from trusted academies.</p>
                <ul class="ly-checklist">
                    <li>Quick and free registration</li>
                    <li>Personalized student dashboard</li>
                    <li>Course progress tracking</li>
                    <li>Secure account access</li>
                </ul>
            </div>

            <div class="ly-auth-card">
                <h3>Create account</h3>
                <p class="ly-auth-card-lead">Fill in your details to create a student account.</p>

                @if($errors->any())
                    <div class="ly-auth-error">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('student.register.submit') }}" class="ly-auth-form">
                    @csrf
                    @if(!empty($redirect))
                        <input type="hidden" name="redirect" value="{{ $redirect }}">
                    @endif
                    <label>
                        <span>Full name</span>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Your full name">
                    </label>
                    <label>
                        <span>Email</span>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="you@example.com">
                    </label>
                    <label>
                        <span>Phone <em>(optional)</em></span>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+91 ...">
                    </label>
                    <label>
                        <span>Password</span>
                        <input type="password" name="password" required placeholder="Create a password">
                    </label>
                    <label>
                        <span>Confirm password</span>
                        <input type="password" name="password_confirmation" required placeholder="Repeat password">
                    </label>
                    <button type="submit" class="ly-btn ly-btn-green ly-auth-submit">Create student account</button>
                </form>

                <div class="ly-auth-links">
                    <p>Already have an account? <a href="{{ route('student.login', array_filter(['redirect' => $redirect ?? null])) }}">Student login</a></p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
