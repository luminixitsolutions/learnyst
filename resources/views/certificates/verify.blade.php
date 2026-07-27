<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Certificate — {{ config('app.name', 'Learnyst') }}</title>
    <meta name="description" content="Verify the authenticity of a Learnyst certificate using its unique certificate number.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Cormorant+Garamond:wght@600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            color: #0f172a;
            background:
                radial-gradient(ellipse 80% 60% at 50% -10%, rgba(99, 102, 241, 0.14), transparent 60%),
                radial-gradient(ellipse 60% 50% at 100% 50%, rgba(16, 185, 129, 0.08), transparent 55%),
                radial-gradient(ellipse 50% 40% at 0% 80%, rgba(139, 92, 246, 0.08), transparent 50%),
                linear-gradient(180deg, #f8fafc 0%, #eef2ff 45%, #f0fdf4 100%);
        }
        .cv-page {
            max-width: 720px;
            margin: 0 auto;
            padding: 2.5rem 1.25rem 4rem;
        }
        .cv-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: inherit;
            margin-bottom: 2rem;
        }
        .cv-brand-mark {
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 0.85rem;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #10b981 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 800;
            font-size: 1.15rem;
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.28);
        }
        .cv-brand-text { font-weight: 800; font-size: 1.15rem; letter-spacing: -0.02em; }
        .cv-brand-sub { display: block; font-size: 0.7rem; font-weight: 600; color: #64748b; letter-spacing: 0.06em; text-transform: uppercase; }

        .cv-hero { text-align: center; margin-bottom: 2rem; }
        .cv-hero-icon {
            width: 4.5rem;
            height: 4.5rem;
            margin: 0 auto 1.25rem;
            border-radius: 1.25rem;
            background: linear-gradient(145deg, #fff 0%, #f1f5f9 100%);
            border: 1px solid rgba(255,255,255,0.8);
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.08), inset 0 1px 0 #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .cv-hero-icon svg { width: 2rem; height: 2rem; color: #6366f1; }
        .cv-hero h1 {
            margin: 0 0 0.5rem;
            font-size: clamp(1.75rem, 4vw, 2.25rem);
            font-weight: 800;
            letter-spacing: -0.03em;
            line-height: 1.15;
        }
        .cv-hero p { margin: 0; color: #64748b; font-size: 1rem; max-width: 28rem; margin-inline: auto; line-height: 1.55; }

        .cv-card {
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.95);
            border-radius: 1.5rem;
            box-shadow: 0 4px 6px rgba(15, 23, 42, 0.02), 0 20px 50px rgba(15, 23, 42, 0.07);
            overflow: hidden;
        }
        .cv-search { padding: 1.75rem; }
        .cv-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 0.5rem;
            letter-spacing: 0.02em;
        }
        .cv-input-wrap { position: relative; }
        .cv-input-wrap svg {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            width: 1.15rem;
            height: 1.15rem;
            color: #94a3b8;
            pointer-events: none;
        }
        .cv-input {
            width: 100%;
            padding: 0.95rem 1rem 0.95rem 2.75rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 0.9rem;
            font-family: ui-monospace, 'Cascadia Code', monospace;
            font-size: 0.95rem;
            font-weight: 600;
            color: #0f172a;
            background: #fff;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .cv-input:focus {
            outline: none;
            border-color: #818cf8;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
        }
        .cv-input::placeholder { color: #cbd5e1; font-weight: 500; }
        .cv-btn {
            width: 100%;
            margin-top: 1rem;
            padding: 0.95rem 1.25rem;
            border: none;
            border-radius: 0.9rem;
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 700;
            color: #fff;
            cursor: pointer;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 55%, #059669 100%);
            box-shadow: 0 8px 24px rgba(79, 70, 229, 0.28);
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .cv-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 28px rgba(79, 70, 229, 0.32);
        }
        .cv-trust {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1.25rem;
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid #f1f5f9;
        }
        .cv-trust-item {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #64748b;
        }
        .cv-trust-item svg { width: 1rem; height: 1rem; color: #10b981; flex-shrink: 0; }

        /* Result panel */
        .cv-result { border-top: 1px solid #f1f5f9; animation: cvFadeUp 0.45s ease-out; }
        @keyframes cvFadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .cv-result-head {
            padding: 1.5rem 1.75rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }
        .cv-result-head.valid { background: linear-gradient(135deg, rgba(16,185,129,0.08) 0%, rgba(255,255,255,0) 100%); }
        .cv-result-head.warning { background: linear-gradient(135deg, rgba(245,158,11,0.1) 0%, rgba(255,255,255,0) 100%); }
        .cv-result-head.invalid { background: linear-gradient(135deg, rgba(239,68,68,0.08) 0%, rgba(255,255,255,0) 100%); }
        .cv-result-head.notfound { background: linear-gradient(135deg, rgba(239,68,68,0.06) 0%, rgba(255,255,255,0) 100%); }

        .cv-status-icon {
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .cv-status-icon.valid { background: #d1fae5; color: #059669; }
        .cv-status-icon.warning { background: #fef3c7; color: #d97706; }
        .cv-status-icon.invalid { background: #fee2e2; color: #dc2626; }
        .cv-status-icon svg { width: 1.5rem; height: 1.5rem; }

        .cv-status-title { margin: 0 0 0.25rem; font-size: 1.15rem; font-weight: 800; letter-spacing: -0.02em; }
        .cv-status-title.valid { color: #047857; }
        .cv-status-title.warning { color: #b45309; }
        .cv-status-title.invalid { color: #b91c1c; }
        .cv-status-desc { margin: 0; font-size: 0.875rem; color: #64748b; line-height: 1.5; }

        .cv-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            margin-top: 0.65rem;
            padding: 0.3rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .cv-pill.valid { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .cv-pill.warning { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .cv-pill.invalid { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

        .cv-credential {
            margin: 0 1.75rem 1.75rem;
            padding: 1.5rem;
            border-radius: 1.1rem;
            background: linear-gradient(160deg, #fffef8 0%, #fff 40%, #f8fafc 100%);
            border: 1px solid #e2e8f0;
            position: relative;
            overflow: hidden;
        }
        .cv-credential::before {
            content: '';
            position: absolute;
            inset: 10px;
            border: 1px solid rgba(201, 162, 39, 0.25);
            border-radius: 0.75rem;
            pointer-events: none;
        }
        .cv-credential-seal {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 3.5rem;
            height: 3.5rem;
            opacity: 0.18;
        }
        .cv-cred-label {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 0.35rem;
        }
        .cv-cred-name {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e4a8c;
            margin: 0 0 0.5rem;
            line-height: 1.2;
        }
        .cv-cred-course {
            font-size: 0.95rem;
            font-weight: 600;
            color: #334155;
            margin: 0 0 1rem;
        }
        .cv-meta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.85rem 1.25rem;
        }
        @media (max-width: 480px) { .cv-meta-grid { grid-template-columns: 1fr; } }
        .cv-meta-item dt {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #94a3b8;
            margin-bottom: 0.15rem;
        }
        .cv-meta-item dd {
            margin: 0;
            font-size: 0.875rem;
            font-weight: 600;
            color: #1e293b;
            word-break: break-word;
        }
        .cv-meta-item dd.mono { font-family: ui-monospace, monospace; font-size: 0.8rem; }

        .cv-alert {
            margin: 0 1.75rem 1.75rem;
            padding: 0.85rem 1rem;
            border-radius: 0.75rem;
            font-size: 0.8125rem;
            line-height: 1.5;
            display: flex;
            gap: 0.6rem;
            align-items: flex-start;
        }
        .cv-alert.warning { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
        .cv-alert.invalid { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .cv-alert svg { width: 1.1rem; height: 1.1rem; flex-shrink: 0; margin-top: 0.1rem; }

        .cv-footer {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.78rem;
            color: #94a3b8;
        }
        .cv-footer a { color: #6366f1; text-decoration: none; font-weight: 600; }
        .cv-footer a:hover { text-decoration: underline; }

        .cv-empty-hint {
            text-align: center;
            padding: 1.25rem 1.75rem 1.75rem;
            color: #94a3b8;
            font-size: 0.85rem;
        }
        .cv-empty-hint svg { width: 2.5rem; height: 2.5rem; margin-bottom: 0.75rem; opacity: 0.45; }
    </style>
</head>
<body>
@php
    $lifecycle = app(\App\Services\CertificateLifecycleService::class);
    $liveStatus = $certificate ? $lifecycle->resolveStatus($certificate) : null;
    $hasResult = request()->filled('number');
    $statusTone = match ($liveStatus) {
        'valid' => 'valid',
        'expiring_soon' => 'warning',
        'renewal_due', 'expired' => 'invalid',
        default => null,
    };
    $statusTitle = match ($liveStatus) {
        'valid' => 'Authentic Certificate',
        'expiring_soon' => 'Valid — Expiring Soon',
        'renewal_due' => 'Renewal Required',
        'expired' => 'Certificate Expired',
        default => null,
    };
    $statusDesc = match ($liveStatus) {
        'valid' => 'This credential has been verified against our secure registry and is currently active.',
        'expiring_soon' => 'This certificate is authentic but will expire soon. Renewal is recommended.',
        'renewal_due' => 'This certificate is no longer active. The holder must renew to restore validity.',
        'expired' => 'This certificate has passed its validity period and is not currently active.',
        default => null,
    };
    $verifiedAt = now()->format('M d, Y \a\t g:i A');
@endphp

<div class="cv-page">
    <a href="{{ route('home') }}" class="cv-brand">
        <span class="cv-brand-mark">L</span>
        <span>
            <span class="cv-brand-text">{{ config('app.name', 'Learnyst') }}</span>
            <span class="cv-brand-sub">Credential Verification</span>
        </span>
    </a>

    <div class="cv-hero">
        <div class="cv-hero-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <h1>Verify a Certificate</h1>
        <p>Instantly confirm whether a certificate issued through {{ config('app.name', 'Learnyst') }} is genuine and check its current status.</p>
    </div>

    <div class="cv-card">
        <div class="cv-search">
            <form method="GET" action="{{ route('certificates.verify') }}">
                <label class="cv-label" for="cert-number">Certificate number</label>
                <div class="cv-input-wrap">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                    <input
                        id="cert-number"
                        class="cv-input"
                        type="text"
                        name="number"
                        value="{{ request('number') }}"
                        required
                        placeholder="e.g. LUM-CERT-2026-0001"
                        autocomplete="off"
                        spellcheck="false"
                    >
                </div>
                <button type="submit" class="cv-btn">Verify authenticity</button>
            </form>
            <div class="cv-trust">
                <span class="cv-trust-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Secure registry
                </span>
                <span class="cv-trust-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Live status check
                </span>
                <span class="cv-trust-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Instant results
                </span>
            </div>
        </div>

        @if($hasResult)
            @if($certificate)
                <div class="cv-result">
                    <div class="cv-result-head {{ $statusTone }}">
                        <div class="cv-status-icon {{ $statusTone }}">
                            @if($statusTone === 'valid')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            @elseif($statusTone === 'warning')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                            @else
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            @endif
                        </div>
                        <div>
                            <h2 class="cv-status-title {{ $statusTone }}">{{ $statusTitle }}</h2>
                            <p class="cv-status-desc">{{ $statusDesc }}</p>
                            <span class="cv-pill {{ $statusTone }}">{{ $lifecycle->statusLabel($liveStatus) }}</span>
                        </div>
                    </div>

                    <div class="cv-credential">
                        <svg class="cv-credential-seal" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="50" cy="50" r="45" stroke="#c9a227" stroke-width="3"/>
                            <circle cx="50" cy="50" r="35" stroke="#1e4a8c" stroke-width="2"/>
                            <path d="M50 20 L55 40 L75 40 L59 52 L65 72 L50 60 L35 72 L41 52 L25 40 L45 40 Z" fill="#c9a227" opacity="0.6"/>
                        </svg>
                        <p class="cv-cred-label">Awarded to</p>
                        <h3 class="cv-cred-name">{{ $certificate->user?->name ?? '—' }}</h3>
                        <p class="cv-cred-course">{{ $certificate->course?->title ?? 'Certificate of Achievement' }}</p>
                        <dl class="cv-meta-grid">
                            <div class="cv-meta-item">
                                <dt>Certificate ID</dt>
                                <dd class="mono">{{ $certificate->certificate_number }}</dd>
                            </div>
                            <div class="cv-meta-item">
                                <dt>Issued on</dt>
                                <dd>{{ $certificate->issued_at?->format('F j, Y') ?? '—' }}</dd>
                            </div>
                            @if($certificate->expires_at)
                            <div class="cv-meta-item">
                                <dt>Valid until</dt>
                                <dd>{{ $certificate->expires_at->format('F j, Y') }}</dd>
                            </div>
                            @endif
                            <div class="cv-meta-item">
                                <dt>Verified at</dt>
                                <dd>{{ $verifiedAt }}</dd>
                            </div>
                            @if($certificate->renewal_count > 0)
                            <div class="cv-meta-item">
                                <dt>Renewals</dt>
                                <dd>{{ $certificate->renewal_count }} time(s)</dd>
                            </div>
                            @endif
                        </dl>
                    </div>

                    @if($liveStatus === 'expiring_soon')
                    <div class="cv-alert warning">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                        <span>This certificate is still valid but approaching its expiry date. The credential holder should renew through their institute portal.</span>
                    </div>
                    @elseif(in_array($liveStatus, ['renewal_due', 'expired'], true))
                    <div class="cv-alert invalid">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>This credential is not currently active. Contact the issuing institute to request renewal or re-verification.</span>
                    </div>
                    @endif
                </div>
            @else
                <div class="cv-result">
                    <div class="cv-result-head notfound">
                        <div class="cv-status-icon invalid">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <div>
                            <h2 class="cv-status-title invalid">Certificate Not Found</h2>
                            <p class="cv-status-desc">We couldn't find a certificate matching <strong style="font-family:monospace;color:#475569;">{{ request('number') }}</strong>. Please check the number and try again.</p>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="cv-empty-hint">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:block;margin:0 auto 0.75rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Enter the certificate number printed on the credential or shared in the verification link.
            </div>
        @endif
    </div>

    <p class="cv-footer">
        Powered by <a href="{{ route('home') }}">{{ config('app.name', 'Learnyst') }}</a> · Trusted credential verification for institutes worldwide
    </p>
</div>
</body>
</html>
