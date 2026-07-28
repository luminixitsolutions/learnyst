@extends('layouts.app')
@section('title', 'My Applications')
@section('page-title', 'My Applications')
@section('breadcrumb', 'Student / Placements')

@section('content')
<div class="glass-card rounded-2xl overflow-hidden">
    <table class="w-full text-sm panel-table">
        <thead><tr class="text-left"><th class="px-6 py-3">Job</th><th class="px-6 py-3">Status</th><th class="px-6 py-3">Interview</th></tr></thead>
        <tbody>
        @forelse($applications as $app)
            <tr>
                <td class="px-6 py-3">{{ $app->job?->title }}<div class="text-xs text-slate-500">{{ $app->job?->company?->name }}</div></td>
                <td class="px-6 py-3"><x-badge type="info">{{ $app->status }}</x-badge></td>
                <td class="px-6 py-3 text-slate-500">{{ $app->interview_at?->format('M d, Y H:i') ?? '—' }}</td>
            </tr>
        @empty
            <tr><td colspan="3" class="px-6 py-8 text-center text-slate-500">No applications yet.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $applications->links() }}</div>
</div>
@endsection
