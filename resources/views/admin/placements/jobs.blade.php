@extends('layouts.app')
@section('title', 'Job Listings')
@section('page-title', 'Jobs & Internships')
@section('breadcrumb', 'Placements / Jobs')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.placements.jobs.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <x-form-input label="Company" name="placement_company_id" type="select" required>
                @foreach($companies as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
            </x-form-input>
            <x-form-input label="Title" name="title" required />
            <x-form-input label="Type" name="type" type="select" required>
                <option value="job">Job</option>
                <option value="internship">Internship</option>
            </x-form-input>
            <x-form-input label="Location" name="location" />
            <x-form-input label="Employment type" name="employment_type" placeholder="Full-time / Part-time" />
            <x-form-input label="Salary / stipend" name="stipend_or_salary" type="number" step="0.01" />
            <x-form-input label="Closes at" name="closes_at" type="date" />
            <x-form-input label="Description" name="description" type="textarea" class="md:col-span-3" />
            <x-form-input label="Requirements" name="requirements" type="textarea" class="md:col-span-3" />
            <div class="md:col-span-3"><button class="px-5 py-2.5 rounded-xl panel-btn-primary">Post listing</button></div>
        </form>
    </div>
    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($jobs->count())
        <div class="overflow-x-auto">
            <table id="placementJobsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Company</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Apps</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jobs as $job)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $job->title }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $job->company?->name }}</td>
                        <td class="px-6 py-4 text-slate-600 capitalize">{{ $job->type }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $job->applications_count }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No listings." description="Post a job or internship listing above." />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($jobs->count())
    <x-admin.datatable-scripts table-id="placementJobsTable" entity="jobs" :order-column="0" order-direction="desc" export-file-name="placement-jobs" />
@endif
@endpush
