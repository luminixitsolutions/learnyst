@extends('layouts.app')

@section('title', 'Certificates')
@section('page-title', 'Certificates')
@section('breadcrumb', 'Issued certificates')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('admin.certificates.templates') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Manage Templates →</a>
        <a href="{{ route('admin.certificates.renewals.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Renewal Dashboard →</a>
    </div>

    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Issue Certificate</h3>
        <form method="POST" action="{{ route('admin.certificates.issue') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            @csrf
            <x-form-input label="Learner ID" name="user_id" type="number" required placeholder="User ID" />
            <x-form-input label="Course ID" name="course_id" type="number" placeholder="Optional" />
            <x-form-input label="Template" name="certificate_template_id" type="select">
                <option value="">Default</option>
                @foreach($templates as $template)
                    <option value="{{ $template->id }}">{{ $template->name }}</option>
                @endforeach
            </x-form-input>
            <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Issue</button>
        </form>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($certificates->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                        <th class="px-6 py-4">Certificate #</th>
                        <th class="px-6 py-4">Learner</th>
                        <th class="px-6 py-4">Course</th>
                        <th class="px-6 py-4">Issued</th>
                        <th class="px-6 py-4">Expires</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($certificates as $certificate)
                    @php $lifecycle = app(\App\Services\CertificateLifecycleService::class); @endphp
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 text-indigo-600 font-mono text-xs">{{ $certificate->certificate_number }}</td>
                        <td class="px-6 py-4 text-white">{{ $certificate->user?->name }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $certificate->course?->title ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $certificate->issued_at?->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $certificate->expires_at?->format('M d, Y') ?? '—' }}</td>
                        <td class="px-6 py-4"><x-badge :type="$lifecycle->statusBadgeType($certificate->status)">{{ $lifecycle->statusLabel($certificate->status) }}</x-badge></td>
                        <td class="px-6 py-4"><a href="{{ route('certificates.verify', ['number' => $certificate->certificate_number]) }}" target="_blank" class="text-indigo-600 text-sm">Verify</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $certificates->links() }}</div>
        @else
        <x-empty-state title="No certificates issued yet" />
        @endif
    </div>
</div>
@endsection
