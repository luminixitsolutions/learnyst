@extends('layouts.app')

@section('title', 'Resource Usage Report')
@section('page-title', 'Resource Usage Report')
@section('breadcrumb', 'Reports / Resource Usage')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search resource or learner..." :showDateRange="true" />

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($downloads->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                    <th class="px-6 py-4">Resource</th>
                    <th class="px-6 py-4">Learner</th>
                    <th class="px-6 py-4">Downloads</th>
                    <th class="px-6 py-4">Last Accessed</th>
                    <th class="px-6 py-4">Category</th>
                </tr></thead>
                <tbody>
                    @foreach($downloads as $download)
                    <tr>
                        <td class="px-6 py-4 text-slate-800">{{ $download->resource?->title ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $download->user?->name ?? 'Guest' }}</td>
                        <td class="px-6 py-4">1</td>
                        <td class="px-6 py-4 text-slate-500">{{ $download->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $download->resource?->category?->name ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $downloads->links() }}</div>
        @else
        <x-empty-state title="No resource usage data" />
        @endif
    </div>
</div>
@endsection
