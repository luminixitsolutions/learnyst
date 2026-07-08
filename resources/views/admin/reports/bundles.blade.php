@extends('layouts.app')

@section('title', 'Bundle Report')
@section('page-title', 'Bundle Report')
@section('breadcrumb', 'Reports / Bundles')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search bundle title..." />

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($bundles->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                    <th class="px-6 py-4">Bundle</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Courses</th>
                    <th class="px-6 py-4">Enrollments</th>
                    <th class="px-6 py-4">Price</th>
                </tr></thead>
                <tbody>
                    @foreach($bundles as $bundle)
                    <tr>
                        <td class="px-6 py-4"><a href="{{ route('admin.bundles.show', $bundle) }}" class="text-indigo-600">{{ $bundle->title }}</a></td>
                        <td class="px-6 py-4"><x-badge type="info">{{ ucfirst($bundle->status) }}</x-badge></td>
                        <td class="px-6 py-4">{{ $bundle->courses_count }}</td>
                        <td class="px-6 py-4">{{ $bundle->enrollments_count }}</td>
                        <td class="px-6 py-4">₹{{ number_format($bundle->price ?? 0, 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No bundles found" />
        @endif
    </div>
</div>
@endsection
