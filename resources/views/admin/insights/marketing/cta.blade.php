@extends('layouts.app')

@section('title', 'CTA Insights')
@section('page-title', 'CTA Insights')
@section('breadcrumb', 'Insights / Marketing / CTA')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <x-insight-toolbar :backRoute="route('admin.insights.marketing.index')" searchPlaceholder="Search by CTA or name..." :showInfo="true" infoText="CTA performance derived from lead sources." />

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($records->count())
        <div class="overflow-x-auto">
            <table id="ctaInsightsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">CTA Name</th>
                        <th class="px-6 py-4">Page / Location</th>
                        <th class="px-6 py-4">Clicks</th>
                        <th class="px-6 py-4">Views</th>
                        <th class="px-6 py-4">Conversion Rate</th>
                        <th class="px-6 py-4">Last Clicked</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $row)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $row->source ?? 'Direct' }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $row->course?->title ?? 'Homepage' }}</td>
                        <td class="px-6 py-4 text-slate-600">1</td>
                        <td class="px-6 py-4 text-slate-600">1</td>
                        <td class="px-6 py-4 text-slate-600">{{ $row->status === 'converted' ? '100%' : '0%' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $row->updated_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No results found" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($records->count())
    <x-admin.datatable-scripts table-id="ctaInsightsTable" entity="CTA records" :order-column="5" order-direction="desc" export-file-name="cta-insights" />
@endif
@endpush
