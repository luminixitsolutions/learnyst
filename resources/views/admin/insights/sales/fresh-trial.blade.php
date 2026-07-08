@extends('layouts.app')

@section('title', 'Fresh Trial Insights')
@section('page-title', 'Fresh Trial Insights')
@section('breadcrumb', 'Insights / Sales / Fresh Trial')

@section('content')
<div class="space-y-6">
    <x-insight-toolbar :backRoute="route('admin.insights.sales.index')" searchPlaceholder="Search by email..." :showInfo="true" infoText="Learners currently on trial access.">
        <x-slot:filters>
            <input type="date" name="last_access" value="{{ request('last_access') }}" class="px-3 py-2 rounded-xl border border-slate-200 text-sm" placeholder="Last access">
        </x-slot:filters>
    </x-insight-toolbar>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($records->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                    <th class="px-6 py-4">Learner</th><th class="px-6 py-4">Email</th><th class="px-6 py-4">Trial Product</th>
                    <th class="px-6 py-4">Enrolled</th><th class="px-6 py-4">Last Access</th><th class="px-6 py-4">Status</th>
                </tr></thead>
                <tbody>
                    @foreach($records as $row)
                    <tr>
                        <td class="px-6 py-4">{{ $row->user?->name }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $row->user?->email }}</td>
                        <td class="px-6 py-4">{{ $row->course?->title ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $row->enrolled_at?->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $row->user?->last_login_at?->format('M d, Y') ?? '—' }}</td>
                        <td class="px-6 py-4"><x-badge type="info">{{ ucfirst($row->status) }}</x-badge></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $records->links() }}</div>
        @else
        <x-empty-state title="No results found" />
        @endif
    </div>
</div>
@endsection
