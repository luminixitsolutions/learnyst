@extends('layouts.app')

@section('title', 'Renew Certificate')
@section('page-title', 'Renew Certificate')
@section('breadcrumb', 'Certificates / Renew')

@section('content')
<div class="max-w-xl mx-auto glass-card rounded-2xl p-8 space-y-6">
    <div>
        <p class="text-sm text-slate-500">Course</p>
        <p class="font-semibold text-slate-800">{{ $certificate->course?->title ?? 'Certificate' }}</p>
    </div>
    <div>
        <p class="text-sm text-slate-500">Certificate #</p>
        <p class="font-mono text-slate-700">{{ $certificate->certificate_number }}</p>
    </div>
    <div>
        <p class="text-sm text-slate-500">Current status</p>
        <x-badge :type="app(\App\Services\CertificateLifecycleService::class)->statusBadgeType($certificate->status)">
            {{ app(\App\Services\CertificateLifecycleService::class)->statusLabel($certificate->status) }}
        </x-badge>
    </div>
    <div>
        <p class="text-sm text-slate-500">Expires</p>
        <p class="text-slate-800">{{ $certificate->expires_at?->format('M d, Y') ?? '—' }}</p>
    </div>

    @if($certificate->template?->requires_renewal_assessment)
        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-800">
            A refresher assessment may be required before renewal is finalized (configured on template).
        </div>
    @endif

    <div class="pt-2 border-t border-slate-100">
        <p class="text-lg font-bold text-slate-800">
            @if($price > 0)
                Renewal fee: ₹{{ number_format($price, 2) }} + GST
            @else
                Free renewal
            @endif
        </p>
    </div>

    <form method="POST" action="{{ auth()->user()->isAlumni() ? route('alumni.certificates.renew.start', $certificate) : route('learner.certificates.renew.start', $certificate) }}">
        @csrf
        <button type="submit" class="w-full panel-btn-primary justify-center">Proceed to Renew</button>
    </form>
</div>
@endsection
