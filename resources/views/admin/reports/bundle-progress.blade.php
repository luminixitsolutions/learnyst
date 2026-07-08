@extends('layouts.app')

@section('title', 'Bundle Progress Report')
@section('page-title', 'Bundle Progress Report')
@section('breadcrumb', 'Reports / Bundle Progress')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search by title, learner or email...">
        <x-slot:filters>
            <select name="bundle_id" class="px-3 py-2 rounded-xl border border-slate-200 text-sm">
                <option value="">All Bundles</option>
                @foreach($bundles as $bundle)
                    <option value="{{ $bundle->id }}" @selected(request('bundle_id') == $bundle->id)>{{ $bundle->title }}</option>
                @endforeach
            </select>
        </x-slot:filters>
    </x-report-toolbar>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($records->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                    <th class="px-6 py-4">Bundle Name</th>
                    <th class="px-6 py-4">Learner</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Courses Completed</th>
                    <th class="px-6 py-4">Total Courses</th>
                    <th class="px-6 py-4">Progress %</th>
                    <th class="px-6 py-4">Last Activity</th>
                    <th class="px-6 py-4">Status</th>
                </tr></thead>
                <tbody>
                    @foreach($records as $record)
                    @php $meta = $record->meta ?? []; @endphp
                    <tr>
                        <td class="px-6 py-4 text-slate-800">{{ $record->bundle?->title }}</td>
                        <td class="px-6 py-4">{{ $record->user?->name }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $record->user?->email }}</td>
                        <td class="px-6 py-4">{{ $meta['courses_completed'] ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $meta['total_courses'] ?? $record->bundle?->courses()->count() }}</td>
                        <td class="px-6 py-4">{{ number_format($record->progress ?? 0, 0) }}%</td>
                        <td class="px-6 py-4 text-slate-500">{{ $record->updated_at?->format('M d, Y') }}</td>
                        <td class="px-6 py-4"><x-badge type="info">{{ ucfirst($record->status) }}</x-badge></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $records->links() }}</div>
        @else
        <x-empty-state title="No bundle progress data" />
        @endif
    </div>
</div>
@endsection
