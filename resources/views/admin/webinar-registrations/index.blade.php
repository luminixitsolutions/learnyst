@extends('layouts.app')

@section('title', 'Webinar Registrations')
@section('page-title', 'Webinar Registrations')
@section('breadcrumb', 'Marketing / Webinar Registrations')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <form method="GET" class="glass-card rounded-2xl p-4 flex gap-3 items-end">
        <x-form-input label="Webinar" name="webinar_id" type="select" :value="request('webinar_id')">
            <option value="">All</option>
            @foreach($webinars as $w)
                <option value="{{ $w->id }}" @selected(request('webinar_id')==$w->id)>{{ $w->title }}</option>
            @endforeach
        </x-form-input>
        <button class="px-4 py-2.5 rounded-xl panel-btn-primary">Filter</button>
    </form>
    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($registrations->count())
        <div class="overflow-x-auto">
            <table id="webinarRegsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Webinar</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Registered</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($registrations as $reg)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $reg->name }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $reg->email }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $reg->webinar?->title }}</td>
                        <td class="px-6 py-4"><x-badge type="info">{{ $reg->status }}</x-badge></td>
                        <td class="px-6 py-4 text-slate-500">{{ $reg->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No registrations." description="Webinar registrations will appear here." />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($registrations->count())
    <x-admin.datatable-scripts table-id="webinarRegsTable" entity="registrations" :order-column="4" order-direction="desc" export-file-name="webinar-registrations" />
@endif
@endpush
