@extends('layouts.app')

@section('title', $lead->name)
@section('page-title', $lead->name)
@section('breadcrumb', 'CRM / Lead Detail')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="glass-card rounded-2xl p-6">
                <div class="flex flex-wrap justify-between gap-3">
                    <div>
                        <h3 class="text-xl font-bold text-white">{{ $lead->name }}</h3>
                        <p class="text-sm text-slate-400 mt-1">{{ $lead->email }} · {{ $lead->phone ?? 'No phone' }}</p>
                        <p class="text-xs text-slate-500 mt-1">Source: {{ $lead->source ?? '—' }} · Course: {{ $lead->course?->title ?? '—' }}</p>
                    </div>
                    <x-badge type="info">{{ $stages[$lead->stage] ?? $lead->stage }}</x-badge>
                </div>

                <form method="POST" action="{{ route('admin.crm.leads.stage', $lead) }}" class="mt-4 flex flex-wrap gap-2 items-end">
                    @csrf
                    <x-form-input label="Move stage" name="stage" type="select" :value="$lead->stage">
                        @foreach($stages as $key => $label)
                            <option value="{{ $key }}" @selected($lead->stage===$key)>{{ $label }}</option>
                        @endforeach
                    </x-form-input>
                    <button class="px-4 py-2.5 rounded-xl panel-btn-primary">Update</button>
                </form>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                    <form method="POST" action="{{ route('admin.crm.leads.assign', $lead) }}" class="flex gap-2 items-end">
                        @csrf
                        <x-form-input label="Assign counselor" name="assigned_to" type="select" :value="$lead->assigned_to">
                            @foreach($counselors as $c)
                                <option value="{{ $c->id }}" @selected($lead->assigned_to==$c->id)>{{ $c->name }}</option>
                            @endforeach
                        </x-form-input>
                        <button class="px-3 py-2.5 text-sm text-emerald-400">Assign</button>
                    </form>
                    @unless($lead->isConverted())
                    <form method="POST" action="{{ route('admin.crm.leads.convert', $lead) }}" class="flex gap-2 items-end">
                        @csrf
                        <x-form-input label="Admit + enroll" name="course_id" type="select">
                            <option value="">Optional course</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" @selected($lead->course_id==$course->id)>{{ $course->title }}</option>
                            @endforeach
                        </x-form-input>
                        <button class="px-3 py-2.5 text-sm text-sky-400" onclick="return confirm('Convert to learner?')">Convert</button>
                    </form>
                    @else
                    <p class="text-sm text-emerald-400 self-end">Converted → {{ $lead->convertedUser?->email }}</p>
                    @endunless
                </div>
            </div>

            <div class="glass-card rounded-2xl p-6">
                <h4 class="font-bold text-white mb-3">Notes</h4>
                <form method="POST" action="{{ route('admin.crm.leads.notes', $lead) }}" class="mb-4">
                    @csrf
                    <x-form-input label="Add note" name="body" type="textarea" required />
                    <button class="mt-2 px-4 py-2 rounded-xl panel-btn-primary text-sm">Save note</button>
                </form>
                @foreach($lead->leadNotes as $note)
                <div class="py-3 border-t border-slate-700/50">
                    <div class="text-sm text-slate-300">{{ $note->body }}</div>
                    <div class="text-xs text-slate-500 mt-1">{{ $note->user?->name }} · {{ $note->created_at->diffForHumans() }}</div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="space-y-6">
            <div class="glass-card rounded-2xl p-6">
                <h4 class="font-bold text-white mb-3">Follow-up</h4>
                <form method="POST" action="{{ route('admin.crm.leads.follow-ups', $lead) }}" class="space-y-3">
                    @csrf
                    <x-form-input label="Title" name="title" required />
                    <x-form-input label="Due" name="due_at" type="datetime-local" />
                    <x-form-input label="Notes" name="notes" type="textarea" />
                    <button class="px-4 py-2 rounded-xl panel-btn-primary text-sm">Schedule</button>
                </form>
                <div class="mt-4 space-y-2">
                    @foreach($lead->followUps as $fu)
                    <div class="flex justify-between gap-2 text-sm p-2 rounded-lg bg-slate-800/50">
                        <div>
                            <div class="text-white">{{ $fu->title }}</div>
                            <div class="text-xs text-slate-500">{{ $fu->due_at?->format('M d H:i') }} · {{ $fu->status }}</div>
                        </div>
                        @if($fu->status==='pending')
                        <form method="POST" action="{{ route('admin.crm.follow-ups.complete', $fu) }}">@csrf
                            <button class="text-emerald-400 text-xs">Done</button>
                        </form>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="glass-card rounded-2xl p-6">
                <h4 class="font-bold text-white mb-3">Call log</h4>
                <form method="POST" action="{{ route('admin.crm.leads.calls', $lead) }}" class="space-y-3">
                    @csrf
                    <x-form-input label="Direction" name="direction" type="select">
                        <option value="outbound">Outbound</option>
                        <option value="inbound">Inbound</option>
                    </x-form-input>
                    <x-form-input label="Outcome" name="outcome" type="select">
                        @foreach(['connected','no_answer','busy','voicemail','wrong_number','other'] as $o)
                            <option value="{{ $o }}">{{ ucfirst(str_replace('_',' ',$o)) }}</option>
                        @endforeach
                    </x-form-input>
                    <x-form-input label="Duration (sec)" name="duration_seconds" type="number" />
                    <x-form-input label="Notes" name="notes" type="textarea" />
                    <button class="px-4 py-2 rounded-xl panel-btn-primary text-sm">Log call</button>
                </form>
            </div>

            <div class="glass-card rounded-2xl p-6">
                <h4 class="font-bold text-white mb-3">WhatsApp / Email (stub)</h4>
                <form method="POST" action="{{ route('admin.crm.leads.messages', $lead) }}" class="space-y-3">
                    @csrf
                    <x-form-input label="Channel" name="channel" type="select">
                        <option value="whatsapp">WhatsApp</option>
                        <option value="email">Email</option>
                        <option value="sms">SMS</option>
                    </x-form-input>
                    <x-form-input label="Subject" name="subject" />
                    <x-form-input label="Body" name="body" type="textarea" required />
                    <button class="px-4 py-2 rounded-xl panel-btn-primary text-sm">Log message</button>
                </form>
                @foreach($lead->messages->take(5) as $msg)
                <div class="mt-3 text-xs text-slate-400 border-t border-slate-700/50 pt-2">
                    <span class="uppercase">{{ $msg->channel }}</span> · {{ $msg->status }} · {{ $msg->sent_at?->diffForHumans() }}
                    <div class="text-slate-300 mt-1">{{ \Illuminate\Support\Str::limit($msg->body, 80) }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
