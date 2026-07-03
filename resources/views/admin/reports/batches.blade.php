@extends('layouts.app')

@section('title', 'Batches Report')
@section('page-title', 'Batches Report')
@section('breadcrumb', 'Reports / Batches')

@section('content')
<div class="space-y-6">
    <div class="flex justify-end">
        <a href="{{ route('admin.reports.index') }}" class="text-sm text-slate-500 hover:text-white">← All Reports</a>
    </div>
    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                        <th class="px-6 py-4">Batch</th>
                        <th class="px-6 py-4">Course</th>
                        <th class="px-6 py-4">Learners</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $batch)
                    <tr>
                        <td class="px-6 py-4"><a href="{{ route('admin.batches.show', $batch) }}" class="text-white hover:text-indigo-600">{{ $batch->title }}</a></td>
                        <td class="px-6 py-4 text-slate-500">{{ $batch->course?->title }}</td>
                        <td class="px-6 py-4 text-white">{{ $batch->learners_count }}</td>
                        <td class="px-6 py-4"><x-badge type="info">{{ ucfirst($batch->status) }}</x-badge></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-slate-500">No batches</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
