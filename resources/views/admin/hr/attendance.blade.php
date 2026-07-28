@extends('layouts.app')
@section('title', 'Attendance')
@section('page-title', 'Attendance')
@section('breadcrumb', 'HR / Attendance')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
    @endif

    <div class="glass-card rounded-2xl p-4 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <x-form-input label="Date" name="date" type="date" :value="$date" />
            <button class="panel-btn-primary text-sm">Load</button>
            @if(! $isToday)
                <a href="{{ route('admin.hr.attendance', ['date' => now()->toDateString()]) }}" class="panel-btn-secondary text-sm">Today</a>
            @endif
        </form>
        <div class="text-sm text-slate-600">
            @if($isToday)
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">Live punch for today · {{ now()->format('h:i A') }}</span>
            @else
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">Viewing {{ \Illuminate\Support\Carbon::parse($date)->format('M d, Y') }} — punch still allowed</span>
            @endif
        </div>
    </div>

    <form method="POST" action="{{ route('admin.hr.attendance.store') }}" class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @csrf
        <input type="hidden" name="work_date" value="{{ $date }}">
        @if($employees->count())
        <div class="overflow-x-auto">
            <table id="attendanceTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Employee</th>
                        <th class="px-6 py-4">Punch In</th>
                        <th class="px-6 py-4">Punch Out</th>
                        <th class="px-6 py-4">Hours</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Notes</th>
                        <th class="px-6 py-4 text-right">Punch</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $i => $e)
                        @php
                            $row = $rows[$e->id] ?? null;
                            $checkIn = $row?->check_in ? substr((string) $row->check_in, 0, 5) : null;
                            $checkOut = $row?->check_out ? substr((string) $row->check_out, 0, 5) : null;
                            $hours = '—';
                            if ($checkIn && $checkOut) {
                                try {
                                    $mins = \Illuminate\Support\Carbon::parse($date.' '.$checkIn)
                                        ->diffInMinutes(\Illuminate\Support\Carbon::parse($date.' '.$checkOut));
                                    $hours = sprintf('%d:%02d', intdiv($mins, 60), $mins % 60);
                                } catch (\Throwable) {
                                    $hours = '—';
                                }
                            }
                        @endphp
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4">
                            <div class="font-medium text-slate-800">{{ $e->name }}</div>
                            <div class="text-xs text-slate-500">{{ $e->employee_code ?: ($e->department ?? '—') }}</div>
                            <input type="hidden" name="entries[{{ $i }}][employee_id]" value="{{ $e->id }}">
                        </td>
                        <td class="px-6 py-4">
                            <input type="time" name="entries[{{ $i }}][check_in]" value="{{ $checkIn }}" class="panel-input text-sm w-[8.5rem]">
                        </td>
                        <td class="px-6 py-4">
                            <input type="time" name="entries[{{ $i }}][check_out]" value="{{ $checkOut }}" class="panel-input text-sm w-[8.5rem]">
                        </td>
                        <td class="px-6 py-4 text-slate-700 font-medium">{{ $hours }}</td>
                        <td class="px-6 py-4">
                            <select name="entries[{{ $i }}][status]" class="panel-input text-sm">
                                @foreach(['present','absent','half_day','leave','holiday'] as $st)
                                    <option value="{{ $st }}" @selected(($row->status ?? 'present')===$st)>{{ ucfirst(str_replace('_',' ',$st)) }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-6 py-4">
                            <input type="text" name="entries[{{ $i }}][notes]" value="{{ $row->notes ?? '' }}" class="panel-input text-sm min-w-[10rem]" placeholder="Optional">
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="inline-flex flex-wrap gap-2 justify-end">
                                @if(! $checkIn)
                                    <button type="submit" form="punch-in-{{ $e->id }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white">Punch In</button>
                                @else
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-emerald-50 text-emerald-700">In {{ $checkIn }}</span>
                                @endif

                                @if($checkIn && ! $checkOut)
                                    <button type="submit" form="punch-out-{{ $e->id }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white">Punch Out</button>
                                @elseif($checkOut)
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-indigo-50 text-indigo-700">Out {{ $checkOut }}</span>
                                @else
                                    <button type="button" disabled class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-400 cursor-not-allowed">Punch Out</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 flex flex-wrap gap-3 items-center justify-between">
            <p class="text-xs text-slate-500">Use Punch In/Out for live time, or edit times manually and click Save attendance.</p>
            <button class="panel-btn-primary">Save attendance</button>
        </div>
        @else
        <x-empty-state title="No employees" description="Add active employees before marking attendance." />
        @endif
    </form>
</div>

@foreach($employees as $e)
    <form id="punch-in-{{ $e->id }}" method="POST" action="{{ route('admin.hr.attendance.punch-in') }}" class="hidden">
        @csrf
        <input type="hidden" name="employee_id" value="{{ $e->id }}">
        <input type="hidden" name="work_date" value="{{ $date }}">
    </form>
    <form id="punch-out-{{ $e->id }}" method="POST" action="{{ route('admin.hr.attendance.punch-out') }}" class="hidden">
        @csrf
        <input type="hidden" name="employee_id" value="{{ $e->id }}">
        <input type="hidden" name="work_date" value="{{ $date }}">
    </form>
@endforeach
@endsection

@push('scripts')
@if($employees->count())
    <x-admin.datatable-scripts table-id="attendanceTable" entity="employees" :order-column="0" order-direction="asc" :action-column="6" export-file-name="attendance" :page-length="100" />
@endif
@endpush
