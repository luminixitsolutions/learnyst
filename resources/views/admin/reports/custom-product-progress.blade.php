@extends('layouts.app')

@section('title', 'Custom Product Progress')
@section('page-title', 'Custom Product Progress Report')
@section('breadcrumb', 'Reports / Custom Product Progress')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search by title, learner or email..." :showDateRange="true" />

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($records->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                    <th class="px-6 py-4">Product Name</th>
                    <th class="px-6 py-4">Learner</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Progress</th>
                    <th class="px-6 py-4">Completed Content</th>
                    <th class="px-6 py-4">Last Activity</th>
                    <th class="px-6 py-4">Status</th>
                </tr></thead>
                <tbody>
                    @foreach($records as $record)
                    <tr>
                        <td class="px-6 py-4">{{ $record->course?->title ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $record->user?->name }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $record->user?->email }}</td>
                        <td class="px-6 py-4">{{ number_format($record->progress ?? 0, 0) }}%</td>
                        <td class="px-6 py-4">{{ ($record->meta['completed_content'] ?? '—') }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $record->updated_at?->format('M d, Y') }}</td>
                        <td class="px-6 py-4"><x-badge type="info">{{ ucfirst($record->status) }}</x-badge></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $records->links() }}</div>
        @else
        <x-empty-state title="No custom product progress" />
        @endif
    </div>
</div>
@endsection
