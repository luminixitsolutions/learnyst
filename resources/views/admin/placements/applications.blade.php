@extends('layouts.app')
@section('title', 'Applications')
@section('page-title', 'Applications')
@section('breadcrumb', 'Placements / Applications')

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl overflow-hidden">
        <table class="w-full text-sm panel-table">
            <thead><tr class="text-left">
                <th class="px-6 py-4">Candidate</th><th class="px-6 py-4">Job</th><th class="px-6 py-4">Status</th><th class="px-6 py-4">Interview</th><th class="px-6 py-4"></th>
            </tr></thead>
            <tbody>
            @forelse($applications as $app)
                <tr>
                    <td class="px-6 py-4 text-white">{{ $app->user?->name }}
                        @if($app->resumeUrl())<div><a href="{{ $app->resumeUrl() }}" target="_blank" class="text-xs text-emerald-400">Resume</a></div>@endif
                    </td>
                    <td class="px-6 py-4 text-slate-400">{{ $app->job?->title }}</td>
                    <td class="px-6 py-4"><x-badge type="info">{{ $app->status }}</x-badge></td>
                    <td class="px-6 py-4 text-slate-400 text-xs">{{ $app->interview_at?->format('M d, Y H:i') ?? '—' }}</td>
                    <td class="px-6 py-4">
                        <form method="POST" action="{{ route('admin.placements.applications.update', $app) }}" class="space-y-2 min-w-[200px]">
                            @csrf
                            <select name="status" class="w-full rounded-lg bg-slate-800 border-slate-600 text-white text-xs">
                                @foreach(['applied','shortlisted','interview','offered','rejected','hired'] as $st)
                                    <option value="{{ $st }}" @selected($app->status===$st)>{{ $st }}</option>
                                @endforeach
                            </select>
                            <input type="datetime-local" name="interview_at" value="{{ $app->interview_at?->format('Y-m-d\TH:i') }}" class="w-full rounded-lg bg-slate-800 border-slate-600 text-white text-xs">
                            <input type="text" name="interview_mode" value="{{ $app->interview_mode }}" placeholder="Zoom / campus" class="w-full rounded-lg bg-slate-800 border-slate-600 text-white text-xs">
                            <button class="text-emerald-400 text-xs">Save</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">No applications.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $applications->links() }}</div>
    </div>
</div>
@endsection
