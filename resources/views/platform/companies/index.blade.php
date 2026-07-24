@extends('layouts.app')

@section('title', 'Institutes')
@section('page-title', 'Institutes')
@section('breadcrumb', 'Platform Admin / Institutes')

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl overflow-hidden">
        <table class="w-full text-sm panel-table">
            <thead>
                <tr class="text-left">
                    <th class="px-6 py-4">Institute</th>
                    <th class="px-6 py-4">Owner Email</th>
                    <th class="px-6 py-4">Courses</th>
                    <th class="px-6 py-4">Visibility</th>
                    <th class="px-6 py-4">Created</th>
                    <th class="px-6 py-4"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($companies as $company)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($company->logoUrl())
                                    <img src="{{ $company->logoUrl() }}" alt="" class="h-10 w-10 rounded-xl object-cover border border-slate-200">
                                @else
                                    <span class="h-10 w-10 rounded-xl bg-slate-900 text-white text-xs font-semibold inline-flex items-center justify-center">{{ $company->initials() }}</span>
                                @endif
                                <div>
                                    <div class="font-medium text-slate-800">{{ $company->name }}</div>
                                    @if($company->city)
                                        <div class="text-xs text-slate-400">{{ $company->city }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $company->email ?: ($company->owner->email ?? '—') }}</td>
                        <td class="px-6 py-4">{{ number_format($company->courses_count) }}</td>
                        <td class="px-6 py-4">
                            <x-badge :type="$company->is_public ? 'success' : 'warning'">
                                {{ $company->is_public ? 'Public' : 'Hidden' }}
                            </x-badge>
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ optional($company->created_at)->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('website.companies.show', $company->slug) }}" target="_blank" class="text-indigo-600 text-sm">View profile →</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-slate-500">No institutes registered yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($companies->hasPages())
        <div>{{ $companies->links() }}</div>
    @endif
</div>
@endsection
