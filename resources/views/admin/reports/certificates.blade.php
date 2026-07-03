@extends('layouts.app')

@section('title', 'Certificates Report')
@section('page-title', 'Certificates Report')
@section('breadcrumb', 'Reports / Certificates')

@section('content')
<div class="space-y-6">
    <div class="flex justify-end">
        <a href="{{ route('admin.reports.index') }}" class="text-sm text-slate-500 hover:text-white">← All Reports</a>
    </div>
    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                        <th class="px-6 py-4">Certificate #</th>
                        <th class="px-6 py-4">Learner</th>
                        <th class="px-6 py-4">Course</th>
                        <th class="px-6 py-4">Issued</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($certificates as $certificate)
                    <tr>
                        <td class="px-6 py-4 font-mono text-indigo-600 text-xs">{{ $certificate->certificate_number }}</td>
                        <td class="px-6 py-4 text-white">{{ $certificate->user?->name }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $certificate->course?->title ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $certificate->issued_at?->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $certificates->links() }}</div>
    </div>
</div>
@endsection
