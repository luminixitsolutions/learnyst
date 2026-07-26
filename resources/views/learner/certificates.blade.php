@extends('layouts.app')

@section('title', 'Certificates')
@section('page-title', 'My Certificates')
@section('breadcrumb', 'Student Panel / Certificates')

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl overflow-hidden">
        <table class="w-full text-sm panel-table">
            <thead>
                <tr class="text-left">
                    <th class="px-6 py-4">Course</th>
                    <th class="px-6 py-4">Certificate No.</th>
                    <th class="px-6 py-4">Issued</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($certificates as $certificate)
                    <tr>
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $certificate->course?->title ?? 'Certificate' }}</td>
                        <td class="px-6 py-4 font-mono text-slate-500">{{ $certificate->certificate_number }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $certificate->issued_at?->format('M d, Y') ?? '—' }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('learner.certificates.download', $certificate) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition">
                                Download
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-slate-500">No certificates yet. Complete courses to earn certificates.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $certificates->links() }}
</div>
@endsection
