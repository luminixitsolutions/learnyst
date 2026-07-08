@extends('layouts.app')

@section('title', 'Platform Users')
@section('page-title', 'Platform Users')
@section('breadcrumb', 'Platform Admin / Users')

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl overflow-hidden">
        @if($users->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                    <th class="px-6 py-4">Name</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Role</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Last Login</th>
                </tr></thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="px-6 py-4 font-medium">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $user->email }}</td>
                        <td class="px-6 py-4 capitalize">{{ str_replace('-', ' ', $user->role?->slug ?? '—') }}</td>
                        <td class="px-6 py-4"><x-badge :type="$user->is_active ? 'success' : 'danger'">{{ $user->is_active ? 'Active' : 'Inactive' }}</x-badge></td>
                        <td class="px-6 py-4 text-slate-500">{{ $user->last_login_at?->format('M d, Y H:i') ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $users->links() }}</div>
        @else
        <x-empty-state title="No users found" />
        @endif
    </div>
</div>
@endsection
