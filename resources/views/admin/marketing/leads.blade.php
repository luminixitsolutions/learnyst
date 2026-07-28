@extends('layouts.app')

@section('title', 'Leads')
@section('page-title', 'Leads')
@section('breadcrumb', 'Marketing / Leads')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Add Lead</h3>
        <form method="POST" action="{{ route('admin.marketing.leads.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <x-form-input label="Name" name="name" required />
            <x-form-input label="Email" name="email" type="email" required />
            <x-form-input label="Phone" name="phone" />
            <x-form-input label="Source" name="source" placeholder="website, referral, webinar..." />
            <x-form-input label="Course" name="course_id" type="select">
                <option value="">— None —</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                @endforeach
            </x-form-input>
            <x-form-input label="Assign Counselor" name="assigned_to" type="select">
                <option value="">— Unassigned —</option>
                @foreach($counselors as $counselor)
                    <option value="{{ $counselor->id }}">{{ $counselor->name }}</option>
                @endforeach
            </x-form-input>
            <x-form-input label="Status" name="status" type="select">
                <option value="new">New</option>
                <option value="contacted">Contacted</option>
                <option value="qualified">Qualified</option>
                <option value="lost">Lost</option>
            </x-form-input>
            <x-form-input label="Notes" name="notes" type="textarea" class="md:col-span-2" />
            <div class="md:col-span-3">
                <button type="submit" class="panel-btn-primary">Add Lead</button>
            </div>
        </form>
    </div>

    <div class="glass-card rounded-2xl p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            <x-form-input label="Search" name="search" :value="request('search')" placeholder="Name, email, phone" />
            <x-form-input label="Status" name="status" type="select" :value="request('status')">
                <option value="">All</option>
                @foreach(['new','contacted','qualified','converted','lost'] as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </x-form-input>
            <x-form-input label="Source" name="source" :value="request('source')" />
            <div class="flex items-end gap-2 pb-0.5">
                <button type="submit" class="panel-btn-primary text-sm">Filter</button>
                <a href="{{ route('admin.marketing.leads') }}" class="panel-btn-secondary text-sm">Reset</a>
            </div>
        </form>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($leads->count())
        <div class="overflow-x-auto">
            <table id="leadsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-3">Lead</th>
                        <th class="px-6 py-3">Course</th>
                        <th class="px-6 py-3">Source</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Counselor</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3 min-w-[280px]">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leads as $lead)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 align-top">
                            <div class="font-medium text-slate-800">{{ $lead->name }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">{{ $lead->email }}</div>
                            @if($lead->phone)
                                <div class="text-xs text-slate-500">{{ $lead->phone }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 align-top text-slate-600">{{ $lead->course?->title ?? '—' }}</td>
                        <td class="px-6 py-4 align-top">
                            <span class="text-xs text-slate-600 break-all">{{ $lead->source ?? '—' }}</span>
                        </td>
                        <td class="px-6 py-4 align-top">
                            @php
                                $badge = match ($lead->status) {
                                    'converted' => 'success',
                                    'lost' => 'danger',
                                    'qualified', 'contacted' => 'warning',
                                    default => 'info',
                                };
                            @endphp
                            <x-badge :type="$badge">{{ ucfirst($lead->status ?? 'new') }}</x-badge>
                        </td>
                        <td class="px-6 py-4 align-top text-slate-600">{{ $lead->assignee?->name ?? '—' }}</td>
                        <td class="px-6 py-4 align-top text-slate-500 whitespace-nowrap">{{ $lead->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 align-top">
                            @if(! $lead->isConverted())
                                <div class="space-y-2 max-w-xs">
                                    <form method="POST" action="{{ route('admin.marketing.leads.assign', $lead) }}" class="flex items-center gap-2">
                                        @csrf
                                        <select name="assigned_to" class="panel-input text-sm flex-1 min-w-0">
                                            <option value="">Assign counselor…</option>
                                            @foreach($counselors as $counselor)
                                                <option value="{{ $counselor->id }}" @selected((int) $lead->assigned_to === (int) $counselor->id)>{{ $counselor->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="panel-btn-secondary text-xs shrink-0 px-3 py-2">Save</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.marketing.leads.convert', $lead) }}" class="flex items-center gap-2">
                                        @csrf
                                        <select name="course_id" class="panel-input text-sm flex-1 min-w-0">
                                            <option value="">Course (optional)</option>
                                            @foreach($courses as $course)
                                                <option value="{{ $course->id }}" @selected((int) $lead->course_id === (int) $course->id)>{{ $course->title }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="panel-btn-primary text-xs shrink-0 px-3 py-2" onclick="return confirm('Convert this lead to a learner?')">Convert</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-xs font-medium text-emerald-700">Converted → {{ $lead->convertedUser?->email ?? 'learner' }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <div class="p-8"><x-empty-state title="No leads yet" description="Add a lead above or capture enquiries from your public institute page." /></div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($leads->count())
    <x-admin.datatable-scripts table-id="leadsTable" entity="leads" :order-column="5" order-direction="desc" :action-column="6" export-file-name="leads" />
@endif
@endpush
