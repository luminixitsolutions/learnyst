@extends('layouts.app')

@section('title', $certificate->certificate_number)
@section('page-title', $certificate->certificate_number)
@section('breadcrumb', 'Platform Admin / Academic / Certificate')

@section('content')
<div class="space-y-6 max-w-4xl">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('platform.academic.certificates') }}" class="text-sm text-slate-500 hover:text-slate-800">← All certificates</a>
        @if($institute?->is_active)
            <form method="POST" action="{{ route('platform.companies.enter-panel', $institute) }}">@csrf
                <button class="panel-btn-primary text-sm">Open institute panel</button>
            </form>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-stat-card title="Status" :value="str_replace('_',' ', ucfirst($certificate->status))" />
        <x-stat-card title="Issued" :value="$certificate->issued_at?->format('M d, Y') ?? '—'" />
        <x-stat-card title="Institute" :value="$institute?->name ?? '—'" />
    </div>

    <div class="glass-card rounded-2xl p-6 space-y-4">
        <h3 class="text-lg font-bold text-slate-800">Certificate details</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div><dt class="text-slate-500">Student</dt><dd class="font-medium">{{ $certificate->user?->name }} · {{ $certificate->user?->email }}</dd></div>
            <div>
                <dt class="text-slate-500">Course</dt>
                <dd class="font-medium">
                    @if($certificate->course)
                        <a href="{{ route('platform.academic.courses.show', $certificate->course) }}" class="text-indigo-600 hover:underline">{{ $certificate->course->title }}</a>
                    @else
                        —
                    @endif
                </dd>
            </div>
            <div><dt class="text-slate-500">Template</dt><dd class="font-medium">{{ $certificate->template?->name ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">Expires</dt><dd class="font-medium">{{ $certificate->expires_at?->format('M d, Y') ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">Renewals</dt><dd class="font-medium">{{ $certificate->renewal_count ?? 0 }}</dd></div>
            <div><dt class="text-slate-500">PDF</dt><dd class="font-medium">{{ $certificate->pdf_path ? 'Available' : '—' }}</dd></div>
        </dl>
    </div>
</div>
@endsection
