@extends('layouts.app')
@section('title', 'Placement Companies')
@section('page-title', 'Recruiters')
@section('breadcrumb', 'Placements / Companies')

@section('content')
<div class="space-y-6">
    <p class="text-sm text-slate-400">Placement recruiters are separate from LMS Company tenants.</p>
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.placements.companies.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <x-form-input label="Company name" name="name" required />
            <x-form-input label="Industry" name="industry" />
            <x-form-input label="Website" name="website" />
            <x-form-input label="Contact name" name="contact_name" />
            <x-form-input label="Contact email" name="contact_email" type="email" />
            <x-form-input label="Contact phone" name="contact_phone" />
            <x-form-input label="About" name="about" type="textarea" class="md:col-span-3" />
            <div class="md:col-span-3"><button class="px-5 py-2.5 rounded-xl panel-btn-primary">Add company</button></div>
        </form>
    </div>
    <div class="glass-card rounded-2xl overflow-hidden">
        <table class="w-full text-sm panel-table">
            <thead><tr class="text-left"><th class="px-6 py-4">Company</th><th class="px-6 py-4">Industry</th><th class="px-6 py-4">Jobs</th></tr></thead>
            <tbody>
            @forelse($companies as $c)
                <tr>
                    <td class="px-6 py-4 text-white">{{ $c->name }}</td>
                    <td class="px-6 py-4 text-slate-400">{{ $c->industry ?? '—' }}</td>
                    <td class="px-6 py-4 text-slate-400">{{ $c->jobs_count }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="px-6 py-8 text-center text-slate-500">No recruiters yet.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $companies->links() }}</div>
    </div>
</div>
@endsection
