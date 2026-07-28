@extends('layouts.app')

@section('title', 'Renewal Trial Insights')
@section('page-title', 'Renewal Trial Insights')
@section('breadcrumb', 'Insights / Sales / Renewal Trial')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <x-insight-toolbar :backRoute="route('admin.insights.sales.index')" searchPlaceholder="Search by email...">
        <x-slot:filters>
            <input type="date" name="last_access" value="{{ request('last_access') }}" class="px-3 py-2 rounded-xl border border-slate-200 text-sm">
        </x-slot:filters>
    </x-insight-toolbar>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($records->count())
        <div class="overflow-x-auto">
            <table id="renewalTrialTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Learner</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Product</th>
                        <th class="px-6 py-4">Expiry Date</th>
                        <th class="px-6 py-4">Last Access</th>
                        <th class="px-6 py-4">Renewal Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $row)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $row->user?->name }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $row->user?->email }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $row->course?->title ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $row->expires_at?->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $row->user?->last_login_at?->format('M d, Y') ?? '—' }}</td>
                        <td class="px-6 py-4"><x-badge :type="$row->expires_at?->isPast() ? 'danger' : 'success'">{{ $row->expires_at?->isPast() ? 'Expired' : 'Active' }}</x-badge></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No results found" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($records->count())
    <x-admin.datatable-scripts table-id="renewalTrialTable" entity="renewals" :order-column="3" order-direction="desc" export-file-name="renewal-trial" />
@endif
@endpush
