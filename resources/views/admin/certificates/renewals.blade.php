@extends('layouts.app')

@section('title', 'Certificate Renewals')
@section('page-title', 'Certificate Renewal Dashboard')
@section('breadcrumb', 'Teaching / Certificate Renewal')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="glass-card rounded-2xl p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Valid (with expiry)</p>
            <p class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['valid'] }}</p>
        </div>
        <div class="glass-card rounded-2xl p-5">
            <p class="text-xs uppercase tracking-wide text-amber-600 font-semibold">Expiring Soon</p>
            <p class="text-2xl font-bold text-amber-700 mt-1">{{ $stats['expiring_soon'] }}</p>
        </div>
        <div class="glass-card rounded-2xl p-5">
            <p class="text-xs uppercase tracking-wide text-red-600 font-semibold">Renewal Due</p>
            <p class="text-2xl font-bold text-red-700 mt-1">{{ $stats['renewal_due'] }}</p>
        </div>
    </div>

    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.certificates.renewals.index') }}" class="px-3 py-1.5 rounded-lg text-sm {{ !request('status') ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600' }}">All</a>
        @foreach(['expiring_soon', 'renewal_due', 'expired', 'valid'] as $filter)
            <a href="{{ route('admin.certificates.renewals.index', ['status' => $filter]) }}"
               class="px-3 py-1.5 rounded-lg text-sm {{ request('status') === $filter ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600' }}">
                {{ $lifecycle->statusLabel($filter) }}
            </a>
        @endforeach
        <a href="{{ route('admin.certificates.templates') }}" class="ml-auto text-sm text-indigo-600 hover:text-indigo-800">Template validity settings →</a>
    </div>

    <form method="POST" action="{{ route('admin.certificates.renewals.bulk') }}">
        @csrf
        <div class="glass-card rounded-2xl overflow-hidden">
            @if($certificates->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm panel-table">
                    <thead>
                        <tr class="text-left">
                            <th class="px-6 py-4 w-10"><input type="checkbox" onclick="document.querySelectorAll('.cert-select').forEach(c => c.checked = this.checked)"></th>
                            <th class="px-6 py-4">Certificate #</th>
                            <th class="px-6 py-4">Learner</th>
                            <th class="px-6 py-4">Course</th>
                            <th class="px-6 py-4">Expires</th>
                            <th class="px-6 py-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($certificates as $certificate)
                        <tr>
                            <td class="px-6 py-4"><input type="checkbox" class="cert-select" name="certificate_ids[]" value="{{ $certificate->id }}"></td>
                            <td class="px-6 py-4 font-mono text-xs text-indigo-600">{{ $certificate->certificate_number }}</td>
                            <td class="px-6 py-4">{{ $certificate->user?->name }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ $certificate->course?->title ?? '—' }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ $certificate->expires_at?->format('M d, Y') ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <x-badge :type="$lifecycle->statusBadgeType($certificate->status)">{{ $lifecycle->statusLabel($certificate->status) }}</x-badge>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-between gap-4">
                {{ $certificates->links() }}
                <button type="submit" class="panel-btn-primary">Bulk Renew Selected</button>
            </div>
            @else
            <x-empty-state title="No certificates match this filter" description="Configure template validity periods to enable periodic certification." />
            @endif
        </div>
    </form>
</div>
@endsection
