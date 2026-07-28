@extends('layouts.app')

@section('title', 'Company Branding')
@section('page-title', 'White-label oversight')
@section('breadcrumb', 'Platform / Branding')

@section('content')
<div class="space-y-6">
    <form method="GET" class="flex gap-3">
        <input name="search" value="{{ request('search') }}" placeholder="Search company or domain" class="rounded-xl border px-3 py-2 text-sm">
        <button class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm">Search</button>
    </form>
    <div class="bg-white rounded-2xl overflow-hidden shadow-sm">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-slate-500 border-b">
                <th class="px-4 py-3">Company</th>
                <th class="px-4 py-3">Domain</th>
                <th class="px-4 py-3">Verified</th>
                <th class="px-4 py-3"></th>
            </tr></thead>
            <tbody>
                @forelse($companies as $company)
                <tr class="border-b">
                    <td class="px-4 py-3">{{ $company->name }}</td>
                    <td class="px-4 py-3 font-mono text-xs">{{ $company->custom_domain ?: '—' }}</td>
                    <td class="px-4 py-3">{{ $company->domain_verified_at ? 'Yes' : 'No' }}</td>
                    <td class="px-4 py-3"><a href="{{ route('platform.branding.show', $company) }}" class="text-emerald-600">View</a></td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No companies.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $companies->links() }}
</div>
@endsection
