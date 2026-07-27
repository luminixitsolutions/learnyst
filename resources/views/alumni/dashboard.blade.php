@extends('layouts.app')

@section('title', 'Alumni Dashboard')
@section('page-title', 'Alumni Dashboard')
@section('breadcrumb', 'Alumni Portal')

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <h2 class="text-lg font-bold text-slate-800">Welcome back, {{ auth()->user()->name }}</h2>
        <p class="text-sm text-slate-600 mt-1">Your alumni network hub. Directory, membership, and mentorship features expand in Phase 3.</p>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 font-semibold">Recent certificates</div>
        @if($certificates->count())
        <table class="w-full text-sm panel-table">
            <tbody>
                @foreach($certificates as $certificate)
                <tr>
                    <td class="px-6 py-4">{{ $certificate->course?->title ?? 'Certificate' }}</td>
                    <td class="px-6 py-4 font-mono text-xs">{{ $certificate->certificate_number }}</td>
                    <td class="px-6 py-4">
                        <x-badge :type="app(\App\Services\CertificateLifecycleService::class)->statusBadgeType($certificate->status)">
                            {{ app(\App\Services\CertificateLifecycleService::class)->statusLabel($certificate->status) }}
                        </x-badge>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="px-6 py-8 text-sm text-slate-500">No certificates on file yet.</p>
        @endif
    </div>
</div>
@endsection
