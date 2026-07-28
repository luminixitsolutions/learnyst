@extends('layouts.app')

@section('title', 'Login history')
@section('page-title', 'Login history')
@section('breadcrumb', 'Security / History')

@section('content')
<div class="glass-card rounded-2xl p-6 overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-slate-500 border-b">
                <th class="py-2">When</th><th>User</th><th>Email</th><th>IP</th><th>Status</th><th>Provider</th>
            </tr>
        </thead>
        <tbody>
        @foreach($history as $row)
            <tr class="border-b border-slate-100">
                <td class="py-2">{{ $row->created_at }}</td>
                <td>{{ $row->user_id }}</td>
                <td>{{ $row->email }}</td>
                <td>{{ $row->ip_address }}</td>
                <td>{{ $row->status }}</td>
                <td>{{ $row->provider }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="mt-4">{{ $history->links() }}</div>
</div>
@endsection
