@extends('layouts.app')

@section('title', 'Alumni Directory')
@section('page-title', 'Alumni Network')
@section('breadcrumb', 'Users / Alumni')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <p class="text-sm text-slate-600">Users with the <strong>Alumni</strong> role. Full directory, membership tiers, and mentorship matching ship in Phase 3.</p>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($alumni->count())
        <div class="overflow-x-auto">
            <table id="alumniTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Certificates</th>
                        <th class="px-6 py-4">Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alumni as $member)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $member->name }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $member->email }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $member->certificates_count }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $member->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No alumni yet" description="Alumni profiles are assigned when learners complete certificate tracks (Phase 3 auto-enrollment)." />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($alumni->count())
    <x-admin.datatable-scripts table-id="alumniTable" entity="alumni" :order-column="0" order-direction="desc" export-file-name="alumni" />
@endif
@endpush
