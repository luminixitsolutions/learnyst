@extends('layouts.app')

@section('title', 'Certificates Report')
@section('page-title', 'Certificates Report')
@section('breadcrumb', 'Reports / Certificates')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search learner or certificate number..." />

    <x-admin.report-datatable table-id="certificatesReportTable" :has-records="$certificates->count() > 0" entity="certificates" :order-column="3" order-direction="desc" export-file-name="certificates-report" empty-title="No certificates issued">
        <thead><tr class="text-left">
            <th>Certificate #</th><th>Learner</th><th>Course</th><th>Issued</th>
        </tr></thead>
        <tbody>
            @foreach($certificates as $certificate)
            <tr>
                <td class="font-mono text-sm">{{ $certificate->certificate_number }}</td>
                <td>{{ $certificate->user?->name }}</td>
                <td class="text-slate-500">{{ $certificate->course?->title }}</td>
                <td class="text-slate-500" data-order="{{ $certificate->issued_at?->timestamp ?? 0 }}">{{ $certificate->issued_at?->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </x-admin.report-datatable>
</div>
@endsection
