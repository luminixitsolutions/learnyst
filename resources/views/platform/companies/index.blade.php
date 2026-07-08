@extends('layouts.app')

@section('title', 'Companies')
@section('page-title', 'Companies')
@section('breadcrumb', 'Platform Admin / Companies')

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl overflow-hidden">
        <table class="w-full text-sm panel-table">
            <thead><tr class="text-left">
                <th class="px-6 py-4">Company Name</th>
                <th class="px-6 py-4">Contact Email</th>
                <th class="px-6 py-4">Users</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4">Created</th>
                <th class="px-6 py-4"></th>
            </tr></thead>
            <tbody>
                <tr>
                    <td class="px-6 py-4 font-medium text-slate-800">{{ $company['name'] }}</td>
                    <td class="px-6 py-4 text-slate-500">{{ $company['email'] }}</td>
                    <td class="px-6 py-4">{{ number_format($company['users']) }}</td>
                    <td class="px-6 py-4"><x-badge type="success">{{ $company['status'] }}</x-badge></td>
                    <td class="px-6 py-4 text-slate-500">{{ $company['created_at'] }}</td>
                    <td class="px-6 py-4"><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 text-sm">Open Company Panel →</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
