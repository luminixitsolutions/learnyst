@extends('layouts.app')

@section('title', 'Bundles Report')
@section('page-title', 'Bundles Report')
@section('breadcrumb', 'Reports / Bundles')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-sm text-slate-500">{{ $bundles->count() }} bundles</p>
        <a href="{{ route('admin.reports.index') }}" class="text-sm text-slate-500 hover:text-white">← All Reports</a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($bundles->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                        <th class="px-6 py-4 font-medium">Bundle</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Courses</th>
                        <th class="px-6 py-4 font-medium">Enrollments</th>
                        <th class="px-6 py-4 font-medium">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bundles as $bundle)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.bundles.show', $bundle) }}" class="text-slate-800 font-semibold hover:text-indigo-600">{{ $bundle->title }}</a>
                        </td>
                        <td class="px-6 py-4">
                            <x-badge :type="match($bundle->status) { 'published' => 'success', 'draft' => 'warning', default => 'default' }">{{ ucfirst($bundle->status) }}</x-badge>
                        </td>
                        <td class="px-6 py-4 text-slate-300">{{ $bundle->courses_count }}</td>
                        <td class="px-6 py-4 text-white">{{ $bundle->enrollments_count }}</td>
                        <td class="px-6 py-4 text-slate-300">₹{{ number_format($bundle->price ?? 0, 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No bundle data" />
        @endif
    </div>
</div>
@endsection
