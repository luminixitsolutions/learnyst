@extends('website.layouts.app')

@section('title')
@yield('signup_title', 'Institute Register') – {{ config('website.brand') }}
@endsection

@section('meta_description', 'Create your Learnyst institute account and start selling courses online. Free trial — no credit card required.')

@push('styles')
<style>
    .ly-signup-section { padding-top: 40px; padding-bottom: 72px; }
    .ly-signup-section .signup-progress {
        display: flex;
        gap: 4px;
        max-width: 640px;
        margin: 0 auto 28px;
        width: 100%;
    }
    .ly-signup-section .signup-progress span {
        flex: 1;
        height: 4px;
        border-radius: 99px;
        background: #e5e7eb;
    }
    .ly-signup-section .signup-progress span.is-done,
    .ly-signup-section .signup-progress span.is-active { background: #22c55e; }
    .ly-signup-section .signup-progress span.is-active { background: #14532d; }
    .signup-main {
        display: flex;
        align-items: flex-start;
        justify-content: center;
    }
    .signup-card {
        width: 100%;
        max-width: 480px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 18px 50px rgba(15, 23, 42, 0.08);
        padding: 32px 28px 28px;
    }
    .signup-wide {
        max-width: 680px;
        box-shadow: none;
        background: transparent;
        border: 0;
        padding: 0;
    }
    .signup-card h1 {
        margin: 0 0 6px;
        font-size: 26px;
        text-align: center;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
    }
    .signup-card .sub {
        text-align: center;
        color: #64748b;
        margin: 0 0 22px;
        font-size: 14px;
        line-height: 1.55;
    }
    .google-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        background: #fff;
        color: #0f172a;
        font-weight: 500;
        font-size: 14px;
        cursor: pointer;
        font-family: inherit;
    }
    .google-btn:hover { border-color: #94a3b8; }
    .divider {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 18px 0;
        color: #94a3b8;
        font-size: 12px;
        font-weight: 600;
    }
    .divider::before,
    .divider::after {
        content: "";
        flex: 1;
        height: 1px;
        background: #e2e8f0;
    }
    .field { margin-bottom: 14px; }
    .field label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 6px;
        color: #334155;
    }
    .field input {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        font-size: 14px;
        font-family: inherit;
        outline: none;
        background: #fff;
        color: #0f172a;
    }
    .field input:focus {
        border-color: #22c55e;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.15);
    }
    .password-wrap { position: relative; }
    .password-wrap input { padding-right: 44px; }
    .password-wrap button {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        border: 0;
        background: transparent;
        cursor: pointer;
        color: #64748b;
        font-size: 16px;
    }
    .primary-btn {
        width: 100%;
        margin-top: 8px;
        padding: 13px 16px;
        border: 0;
        border-radius: 10px;
        background: #16a34a;
        color: #fff;
        font-weight: 700;
        letter-spacing: 0.3px;
        cursor: pointer;
        font-size: 14px;
        font-family: inherit;
    }
    .primary-btn:hover { background: #15803d; }
    .primary-btn:disabled {
        background: #bbf7d0;
        color: #166534;
        cursor: not-allowed;
    }
    .terms {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        margin-top: 14px;
        font-size: 12px;
        color: #475569;
        line-height: 1.45;
    }
    .terms input { margin-top: 2px; }
    .terms a { color: #16a34a; font-weight: 600; }
    .footer-link {
        text-align: center;
        margin-top: 18px;
        color: #64748b;
        font-size: 14px;
    }
    .footer-link a { color: #16a34a; font-weight: 600; text-decoration: none; }
    .footer-link a:hover { text-decoration: underline; }
    .error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
        padding: 10px 12px;
        border-radius: 10px;
        font-size: 13px;
        margin-bottom: 14px;
    }
    .success {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
        padding: 10px 12px;
        border-radius: 10px;
        font-size: 13px;
        margin-bottom: 14px;
    }
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #64748b;
        font-size: 14px;
        margin-bottom: 18px;
        text-decoration: none;
    }
    .back-link:hover { color: #16a34a; }
    .question-title {
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 10px;
        color: #0f172a;
        max-width: 640px;
        line-height: 1.25;
    }
    .question-sub {
        color: #64748b;
        margin: 0 0 24px;
        font-size: 15px;
        max-width: 640px;
        line-height: 1.5;
    }
    .option-list { display: grid; gap: 10px; max-width: 640px; }
    .option-list.two-col { grid-template-columns: 1fr 1fr; }
    .option-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #fff;
        cursor: pointer;
        text-align: left;
        width: 100%;
        font-family: inherit;
        font-size: 14px;
        color: #0f172a;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }
    .option-item .letter {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: #334155;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        flex: 0 0 28px;
    }
    .option-item.is-selected,
    .option-item:hover { border-color: #22c55e; }
    .option-item.is-selected .letter { background: #16a34a; }
    .next-btn {
        margin-top: 18px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 22px;
        background: #16a34a;
        color: #fff;
        border: 0;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
    }
    .next-btn:hover { background: #15803d; }
    .verify-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin: 22px 0 14px;
    }
    .outline-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #fff;
        color: #0f172a;
        font-weight: 500;
        font-size: 14px;
        text-decoration: none;
    }
    .outline-btn:hover { border-color: #16a34a; color: #16a34a; }
    .retry { color: #16a34a; font-weight: 600; font-size: 14px; text-align: center; }
    @media (max-width: 700px) {
        .option-list.two-col { grid-template-columns: 1fr; }
        .question-title { font-size: 22px; }
        .verify-actions { grid-template-columns: 1fr; }
        .signup-card { padding: 24px 20px; }
    }
</style>
@endpush

@section('content')
@php $brand = config('website.brand'); @endphp

<section class="ly-auth-hero">
    <div class="ly-container ly-auth-hero-inner">
        <h1>@yield('signup_heading', 'Institute Register')</h1>
        <p class="ly-product-lead">
            @yield('signup_lead', 'Create your free institute account and start selling courses from your own branded learning platform.')
        </p>
    </div>
</section>

<section class="ly-section ly-section-soft ly-auth-section ly-signup-section">
    <div class="ly-container">
        @hasSection('progress')
            @yield('progress')
        @endif

        <div class="signup-main">
            @yield('signup_body')
        </div>
    </div>
</section>
@endsection
