@extends('layouts.app')

@section('title', 'Leads')
@section('page-title', 'Leads')
@section('breadcrumb', 'Marketing / Leads')

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Add Lead</h3>
        <form method="POST" action="{{ route('admin.marketing.leads.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <x-form-input label="Name" name="name" required />
            <x-form-input label="Email" name="email" type="email" required />
            <x-form-input label="Phone" name="phone" />
            <x-form-input label="Source" name="source" placeholder="website, referral..." />
            <x-form-input label="Notes" name="notes" />
            <div class="md:col-span-3">
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Add Lead</button>
            </div>
        </form>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($leads->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Phone</th>
                        <th class="px-6 py-4">Course</th>
                        <th class="px-6 py-4">Source</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leads as $lead)
                    <tr>
                        <td class="px-6 py-4 text-white">{{ $lead->name }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $lead->email }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $lead->phone ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $lead->course?->title ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $lead->source ?? '—' }}</td>
                        <td class="px-6 py-4"><x-badge type="info">{{ ucfirst($lead->status ?? 'new') }}</x-badge></td>
                        <td class="px-6 py-4 text-slate-500">{{ $lead->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $leads->links() }}</div>
        @else
        <x-empty-state title="No leads yet" />
        @endif
    </div>
</div>
@endsection
