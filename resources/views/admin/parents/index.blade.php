@extends('layouts.app')

@section('title', 'Parent Links')
@section('page-title', 'Parent ↔ Learner Links')
@section('breadcrumb', 'Users / Parent Portal')

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <p class="text-sm text-slate-600">Org admins link parent accounts to learners here. Full parent portal (attendance, fees, payments) ships in Phase 4.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-800">Parent accounts</div>
            @if($parents->count())
            <table class="w-full text-sm panel-table">
                <tbody>
                    @foreach($parents as $parent)
                    <tr>
                        <td class="px-6 py-4">
                            <p class="font-medium">{{ $parent->name }}</p>
                            <p class="text-xs text-slate-500">{{ $parent->email }}</p>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4 border-t">{{ $parents->links() }}</div>
            @else
            <x-empty-state title="No parent accounts" description="Seed or create users with the Parent role." />
            @endif
        </div>

        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-800">Available learners</div>
            <ul class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                @forelse($learners as $learner)
                    <li class="px-6 py-3 text-sm">
                        <span class="font-medium">{{ $learner->name }}</span>
                        <span class="text-slate-500">· {{ $learner->email }}</span>
                    </li>
                @empty
                    <li class="px-6 py-8 text-sm text-slate-500 text-center">No learners in scope.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
