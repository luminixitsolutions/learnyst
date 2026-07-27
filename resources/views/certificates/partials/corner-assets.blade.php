@php
    $verifyUrl = $verifyUrl ?? url('/verify-certificate?number=CERT-PREVIEW123');
    $showQr = $showQr ?? true;
    $qrStyle = $qrStyle ?? '';
    $sealStyle = $sealStyle ?? '';
    $qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&margin=0&data='.urlencode($verifyUrl);
@endphp

@if($showQr)
<div class="cert-qr" @if($qrStyle) style="{{ $qrStyle }}" @endif>
    <img src="{{ $qrImageUrl }}" alt="Certificate verification QR code" loading="lazy">
    <span class="cert-qr-label">Scan to verify</span>
</div>
@endif

<div class="cert-seal" @if($sealStyle) style="{{ $sealStyle }}" @endif aria-hidden="true">
    <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="50" cy="50" r="46" stroke="var(--cert-primary)" stroke-width="3" fill="var(--cert-paper)"/>
        <circle cx="50" cy="50" r="38" stroke="var(--cert-accent)" stroke-width="1.5" fill="none"/>
        <path d="M50 22 L56 38 L73 38 L59 48 L65 64 L50 54 L35 64 L41 48 L27 38 L44 38 Z" fill="var(--cert-accent)" opacity="0.9"/>
        <text x="50" y="78" text-anchor="middle" fill="var(--cert-primary)" font-size="9" font-weight="700" font-family="Arial, sans-serif">VERIFIED</text>
    </svg>
</div>
