@extends('layouts.app')
@section('title', 'Branches')
@section('page-title', 'Branches / Franchise')
@section('breadcrumb', 'Branches')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <p class="text-sm text-slate-500">Branches sit under your company tenant. Platform → Company multi-tenancy is unchanged.</p>
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.branches.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <x-form-input label="Name" name="name" required />
            <x-form-input label="Code" name="code" placeholder="BLR-01" />
            <x-form-input label="City" name="city" />
            <x-form-input label="Phone" name="phone" />
            <x-form-input label="Revenue share %" name="revenue_share_percent" type="number" step="0.01" :value="30" />
            <x-form-input label="Address" name="address" class="md:col-span-3" />
            <div class="md:col-span-3"><button class="px-5 py-2.5 rounded-xl panel-btn-primary">Create branch</button></div>
        </form>
    </div>
    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($branches->count())
        <div class="overflow-x-auto">
            <table id="branchesTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Branch</th>
                        <th class="px-6 py-4">Share %</th>
                        <th class="px-6 py-4">People</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($branches as $b)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 text-slate-800">{{ $b->name }}<div class="text-xs text-slate-500">{{ $b->code }} · {{ $b->city }}</div></td>
                        <td class="px-6 py-4 text-slate-600">{{ $b->revenue_share_percent }}%</td>
                        <td class="px-6 py-4 text-slate-600">{{ $b->users_count }}</td>
                        <td class="px-6 py-4"><a href="{{ route('admin.branches.show', $b) }}" class="text-emerald-600 text-sm">Dashboard</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No branches yet" description="Create a branch using the form above." />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($branches->count())
    <x-admin.datatable-scripts table-id="branchesTable" entity="branches" :order-column="0" order-direction="desc" :action-column="3" export-file-name="branches" />
@endif
@endpush
