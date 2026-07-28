@extends('layouts.app')
@section('title', 'Attendance')
@section('page-title', 'Attendance')
@section('breadcrumb', 'HR / Attendance')

@section('content')
<div class="space-y-6">
    <form method="GET" class="glass-card rounded-2xl p-4 flex gap-3 items-end">
        <x-form-input label="Date" name="date" type="date" :value="$date" />
        <button class="px-4 py-2.5 rounded-xl panel-btn-primary">Load</button>
    </form>
    <form method="POST" action="{{ route('admin.hr.attendance.store') }}" class="glass-card rounded-2xl overflow-hidden">
        @csrf
        <input type="hidden" name="work_date" value="{{ $date }}">
        <table class="w-full text-sm panel-table">
            <thead><tr class="text-left"><th class="px-6 py-4">Employee</th><th class="px-6 py-4">Status</th></tr></thead>
            <tbody>
            @foreach($employees as $i => $e)
                <tr>
                    <td class="px-6 py-4 text-white">{{ $e->name }}
                        <input type="hidden" name="entries[{{ $i }}][employee_id]" value="{{ $e->id }}">
                    </td>
                    <td class="px-6 py-4">
                        <select name="entries[{{ $i }}][status]" class="rounded-lg bg-slate-800 border-slate-600 text-white text-sm">
                            @foreach(['present','absent','half_day','leave','holiday'] as $st)
                                <option value="{{ $st }}" @selected(($rows[$e->id]->status ?? 'present')===$st)>{{ ucfirst(str_replace('_',' ',$st)) }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4"><button class="px-5 py-2.5 rounded-xl panel-btn-primary">Save attendance</button></div>
    </form>
</div>
@endsection
