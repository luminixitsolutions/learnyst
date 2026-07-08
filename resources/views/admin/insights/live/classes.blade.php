@extends('layouts.app')

@section('title', 'Live Classes Insight')
@section('page-title', 'Live Classes Insight')
@section('breadcrumb', 'Insights / Live / Classes')

@section('content')
<div class="space-y-6">
    <x-insight-toolbar :backRoute="route('admin.insights.live.index')" searchPlaceholder="Search class title..." />

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($records->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                    <th class="px-6 py-4">Class Title</th><th class="px-6 py-4">Instructor</th><th class="px-6 py-4">Duration</th>
                    <th class="px-6 py-4">Join Time</th><th class="px-6 py-4">Leave Time</th><th class="px-6 py-4">Engagement</th>
                </tr></thead>
                <tbody>
                    @foreach($records as $row)
                    <tr>
                        <td class="px-6 py-4 font-medium">{{ $row->title }}</td>
                        <td class="px-6 py-4">{{ $row->instructor?->name ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $row->starts_at && $row->ends_at ? $row->starts_at->diffInMinutes($row->ends_at).' min' : '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $row->starts_at?->format('M d, H:i') }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $row->ends_at?->format('M d, H:i') }}</td>
                        <td class="px-6 py-4"><x-badge type="info">{{ ucfirst($row->status ?? 'scheduled') }}</x-badge></td>
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
