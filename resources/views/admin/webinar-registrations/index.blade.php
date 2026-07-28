@extends('layouts.app')

@section('title', 'Webinar Registrations')
@section('page-title', 'Webinar Registrations')
@section('breadcrumb', 'Marketing / Webinar Registrations')

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
    <div class="glass-card rounded-2xl overflow-hidden">
        <table class="w-full text-sm panel-table">
            <thead><tr class="text-left">
                <th class="px-6 py-4">Name</th>
                <th class="px-6 py-4">Email</th>
                <th class="px-6 py-4">Webinar</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4">Registered</th>
            </tr></thead>
            <tbody>
                @forelse($registrations as $reg)
                <tr>
                    <td class="px-6 py-4 text-white">{{ $reg->name }}</td>
                    <td class="px-6 py-4 text-slate-400">{{ $reg->email }}</td>
                    <td class="px-6 py-4 text-slate-400">{{ $reg->webinar?->title }}</td>
                    <td class="px-6 py-4"><x-badge type="info">{{ $reg->status }}</x-badge></td>
                    <td class="px-6 py-4 text-slate-400">{{ $reg->created_at->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">No registrations.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $registrations->links() }}</div>
    </div>
</div>
@endsection
