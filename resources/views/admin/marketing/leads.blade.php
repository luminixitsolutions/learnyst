@extends('layouts.app')

@section('title', 'Leads')
@section('page-title', 'Leads')
@section('breadcrumb', 'Marketing / Leads')

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-white mb-4">Add Lead</h3>
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
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Add Lead</button>
            </div>
        </form>
    </div>

    <div class="glass-card rounded-2xl p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <x-form-input label="Search" name="search" :value="request('search')" placeholder="Name, email, phone" />
            <x-form-input label="Status" name="status" type="select" :value="request('status')">
                <option value="">All</option>
                @foreach(['new','contacted','qualified','converted','lost'] as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </x-form-input>
            <x-form-input label="Source" name="source" :value="request('source')" />
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2.5 rounded-xl panel-btn-primary">Filter</button>
                <a href="{{ route('admin.marketing.leads') }}" class="px-4 py-2.5 rounded-xl text-sm text-slate-400 hover:text-white">Reset</a>
            </div>
        </form>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($leads->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                        <th class="px-6 py-4">Lead</th>
                        <th class="px-6 py-4">Course</th>
                        <th class="px-6 py-4">Source</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Counselor</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leads as $lead)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-white font-medium">{{ $lead->name }}</div>
                            <div class="text-xs text-slate-500">{{ $lead->email }} · {{ $lead->phone ?? '—' }}</div>
                        </td>
                        <td class="px-6 py-4 text-slate-400">{{ $lead->course?->title ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-400">{{ $lead->source ?? '—' }}</td>
                        <td class="px-6 py-4"><x-badge type="info">{{ ucfirst($lead->status ?? 'new') }}</x-badge></td>
                        <td class="px-6 py-4 text-slate-400">{{ $lead->assignee?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-400">{{ $lead->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 space-y-2 min-w-[220px]">
                            @if(! $lead->isConverted())
                            <form method="POST" action="{{ route('admin.marketing.leads.assign', $lead) }}" class="flex gap-2">
                                @csrf
                                <select name="assigned_to" class="flex-1 rounded-lg bg-slate-800 border-slate-600 text-sm text-white">
                                    <option value="">Assign…</option>
                                    @foreach($counselors as $counselor)
                                        <option value="{{ $counselor->id }}" @selected($lead->assigned_to == $counselor->id)>{{ $counselor->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="text-xs text-emerald-400">Save</button>
                            </form>
                            <form method="POST" action="{{ route('admin.marketing.leads.convert', $lead) }}" class="flex gap-2">
                                @csrf
                                <select name="course_id" class="flex-1 rounded-lg bg-slate-800 border-slate-600 text-sm text-white">
                                    <option value="">Course (optional)</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" @selected($lead->course_id == $course->id)>{{ $course->title }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="text-xs text-sky-400 whitespace-nowrap" onclick="return confirm('Convert this lead to a learner?')">Convert</button>
                            </form>
                            @else
                            <span class="text-xs text-emerald-400">Converted → {{ $lead->convertedUser?->email }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-700">{{ $leads->links() }}</div>
        @else
        <x-empty-state title="No leads yet" />
        @endif
    </div>
</div>
@endsection
