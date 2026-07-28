@extends('layouts.app')
@section('title', $employee->name)
@section('page-title', $employee->name)
@section('breadcrumb', 'HR / Employee')

@section('content')
<div class="space-y-6 max-w-4xl">
    <div class="glass-card rounded-2xl p-6 text-sm text-slate-300">
        <p>{{ $employee->email }} · {{ $employee->phone }}</p>
        <p class="mt-1">{{ $employee->department }} / {{ $employee->designation }}</p>
        <p class="mt-2 text-white">Structure: Basic ₹{{ number_format($employee->basic_salary,2) }} + HRA ₹{{ number_format($employee->hra,2) }} + Allow ₹{{ number_format($employee->allowances,2) }} − Ded ₹{{ number_format($employee->deductions,2) }} = <span class="text-emerald-400">₹{{ number_format($employee->netSalary(),2) }}</span></p>
    </div>
    <div class="glass-card rounded-2xl p-6">
        <h3 class="font-bold text-white mb-3">Upload HR document</h3>
        <form method="POST" action="{{ route('admin.hr.documents.store', $employee) }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-3 gap-3">
            @csrf
            <x-form-input label="Title" name="title" required />
            <x-form-input label="Type" name="document_type" placeholder="ID / contract / certificate" />
            <div><label class="text-sm text-slate-300">File</label><input type="file" name="file" required class="mt-1 block w-full text-sm text-slate-400"></div>
            <div class="md:col-span-3"><button class="px-4 py-2 rounded-xl panel-btn-primary text-sm">Upload</button></div>
        </form>
        <div class="mt-4 space-y-2">
            @foreach($employee->documents as $doc)
                <a href="{{ $doc->fileUrl() }}" target="_blank" class="block text-sm text-emerald-400">{{ $doc->title }} ({{ $doc->document_type }})</a>
            @endforeach
        </div>
    </div>
</div>
@endsection
