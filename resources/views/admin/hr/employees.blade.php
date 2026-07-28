@extends('layouts.app')
@section('title', 'Employees')
@section('page-title', 'Employees')
@section('breadcrumb', 'HR / Employees')

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.hr.employees.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <x-form-input label="Name" name="name" required />
            <x-form-input label="Email" name="email" type="email" />
            <x-form-input label="Phone" name="phone" />
            <x-form-input label="Code" name="employee_code" />
            <x-form-input label="Department" name="department" />
            <x-form-input label="Designation" name="designation" />
            <x-form-input label="Joined on" name="joined_on" type="date" />
            <x-form-input label="Link staff user" name="user_id" type="select">
                <option value="">— Optional —</option>
                @foreach($staff as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach
            </x-form-input>
            <x-form-input label="Basic salary" name="basic_salary" type="number" step="0.01" :value="0" />
            <x-form-input label="HRA" name="hra" type="number" step="0.01" :value="0" />
            <x-form-input label="Allowances" name="allowances" type="number" step="0.01" :value="0" />
            <x-form-input label="Deductions" name="deductions" type="number" step="0.01" :value="0" />
            <div class="md:col-span-3"><button class="px-5 py-2.5 rounded-xl panel-btn-primary">Add employee</button></div>
        </form>
    </div>
    <div class="glass-card rounded-2xl overflow-hidden">
        <table class="w-full text-sm panel-table">
            <thead><tr class="text-left">
                <th class="px-6 py-4">Name</th><th class="px-6 py-4">Dept</th><th class="px-6 py-4">Net salary</th><th class="px-6 py-4"></th>
            </tr></thead>
            <tbody>
            @forelse($employees as $e)
                <tr>
                    <td class="px-6 py-4 text-white">{{ $e->name }}<div class="text-xs text-slate-500">{{ $e->employee_code }}</div></td>
                    <td class="px-6 py-4 text-slate-400">{{ $e->department ?? '—' }} / {{ $e->designation ?? '—' }}</td>
                    <td class="px-6 py-4 text-emerald-400">₹{{ number_format($e->netSalary(),2) }}</td>
                    <td class="px-6 py-4"><a href="{{ route('admin.hr.employees.show', $e) }}" class="text-emerald-400 text-sm">Open</a></td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-6 py-8 text-center text-slate-500">No employees.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $employees->links() }}</div>
    </div>
</div>
@endsection
