@extends('layouts.app')

@section('title', 'Certificates Report')
@section('page-title', 'Certificates Report')
@section('breadcrumb', 'Reports / Certificates')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search learner or certificate number..." />

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($certificates->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                    <th class="px-6 py-4">Certificate #</th>
                    <th class="px-6 py-4">Learner</th>
                    <th class="px-6 py-4">Course</th>
                    <th class="px-6 py-4">Issued</th>
                </tr></thead>
                <tbody>
                    @foreach($certificates as $certificate)
                    <tr>
                        <td class="px-6 py-4 font-mono text-sm">{{ $certificate->certificate_number }}</td>
                        <td class="px-6 py-4">{{ $certificate->user?->name }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $certificate->course?->title }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $certificate->issued_at?->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $certificates->links() }}</div>
        @else
        <x-empty-state title="No certificates issued" />
        @endif
    </div>
</div>
@endsection
