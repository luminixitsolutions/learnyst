@extends('layouts.app')
@section('title', 'Branches')
@section('page-title', 'Branches / Franchise')
@section('breadcrumb', 'Branches')

@section('content')
<div class="space-y-6">
    <p class="text-sm text-slate-400">Branches sit under your company tenant. Platform → Company multi-tenancy is unchanged.</p>
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.branches.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <x-form-input label="Name" name="name" required />
            <x-form-input label="Code" name="code" placeholder="BLR-01" />
            <x-form-input label="City" name="city" />
            <x-form-input label="Phone" name="phone" />
            <x-form-input label="Revenue share %" name="revenue_share_percent" type="number" step="0.01" :value="30" />
            <x-form-input label="Address" name="address" class="md:col-span-3" />
            <div class="md:col-span-3"><button class="px-5 py-2.5 rounded-xl panel-btn-primary">Create branch</button></div>
        </form>
    </div>
    <div class="glass-card rounded-2xl overflow-hidden">
        <table class="w-full text-sm panel-table">
            <thead><tr class="text-left">
                <th class="px-6 py-4">Branch</th><th class="px-6 py-4">Share %</th><th class="px-6 py-4">People</th><th class="px-6 py-4"></th>
            </tr></thead>
            <tbody>
            @forelse($branches as $b)
                <tr>
                    <td class="px-6 py-4 text-white">{{ $b->name }}<div class="text-xs text-slate-500">{{ $b->code }} · {{ $b->city }}</div></td>
                    <td class="px-6 py-4 text-slate-400">{{ $b->revenue_share_percent }}%</td>
                    <td class="px-6 py-4 text-slate-400">{{ $b->users_count }}</td>
                    <td class="px-6 py-4"><a href="{{ route('admin.branches.show', $b) }}" class="text-emerald-400 text-sm">Dashboard</a></td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-6 py-8 text-center text-slate-500">No branches yet.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $branches->links() }}</div>
    </div>
</div>
@endsection
