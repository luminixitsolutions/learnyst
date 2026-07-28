@extends('layouts.app')

@section('title', 'Fresh Trial Insights')
@section('page-title', 'Fresh Trial Insights')
@section('breadcrumb', 'Insights / Sales / Fresh Trial')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <x-insight-toolbar :backRoute="route('admin.insights.sales.index')" searchPlaceholder="Search by email..." :showInfo="true" infoText="Learners currently on trial access.">
        <x-slot:filters>
            <input type="date" name="last_access" value="{{ request('last_access') }}" class="px-3 py-2 rounded-xl border border-slate-200 text-sm" placeholder="Last access">
        </x-slot:filters>
    </x-insight-toolbar>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($records->count())
        <div class="overflow-x-auto">
            <table id="freshTrialTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Learner</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Trial Product</th>
                        <th class="px-6 py-4">Enrolled</th>
                        <th class="px-6 py-4">Last Access</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $row)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $row->user?->name }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $row->user?->email }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $row->course?->title ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $row->enrolled_at?->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $row->user?->last_login_at?->format('M d, Y') ?? '—' }}</td>
                        <td class="px-6 py-4"><x-badge type="info">{{ ucfirst($row->status) }}</x-badge></td>
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
    <x-admin.datatable-scripts table-id="freshTrialTable" entity="trial users" :order-column="3" order-direction="desc" export-file-name="fresh-trial" />
@endif
@endpush
