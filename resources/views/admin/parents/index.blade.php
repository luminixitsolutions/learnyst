@extends('layouts.app')

@section('title', 'Parent Links')
@section('page-title', 'Parent ↔ Learner Links')
@section('breadcrumb', 'Users / Parent Portal')

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <p class="text-sm text-slate-600">Link parent accounts to learners so parents can view attendance, progress, fees, and certificates in the Parent portal.</p>
    </div>

    <form method="POST" action="{{ route('admin.parent-links.store') }}" class="glass-card rounded-2xl p-6 grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        @csrf
        <div>
            <label class="text-xs text-slate-500">Parent</label>
            <select name="parent_user_id" required class="panel-input w-full">
                <option value="">Select parent</option>
                @foreach($parentOptions as $parent)
                    <option value="{{ $parent->id }}">{{ $parent->name }} ({{ $parent->email }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs text-slate-500">Learner</label>
            <select name="learner_user_id" required class="panel-input w-full">
                <option value="">Select learner</option>
                @foreach($learners as $learner)
                    <option value="{{ $learner->id }}">{{ $learner->name }} ({{ $learner->email }})</option>
                @endforeach
            </select>
        </div>
        <button class="panel-btn-primary">Create link</button>
    </form>

    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-800">Active links</div>
        <table class="w-full text-sm panel-table">
            <thead><tr><th class="px-6 py-3 text-left">Parent</th><th class="px-6 py-3 text-left">Learner</th><th class="px-6 py-3 text-left">Status</th><th></th></tr></thead>
            <tbody>
            @forelse($links as $link)
                <tr>
                    <td class="px-6 py-3">
                        <div class="font-medium">{{ $link->parent?->name }}</div>
                        <div class="text-xs text-slate-500">{{ $link->parent?->email }}</div>
                    </td>
                    <td class="px-6 py-3">
                        <div class="font-medium">{{ $link->learner?->name }}</div>
                        <div class="text-xs text-slate-500">{{ $link->learner?->email }}</div>
                    </td>
                    <td class="px-6 py-3">{{ $link->status }}</td>
                    <td class="px-6 py-3 text-right">
                        <form method="POST" action="{{ route('admin.parent-links.destroy', $link) }}" onsubmit="return confirm('Remove this link?')">@csrf @method('DELETE')
                            <button class="text-xs text-rose-600 font-semibold">Remove</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-6 py-8 text-center text-slate-500">No links yet.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $links->links() }}</div>
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
                            @if($parent->linkedLearners->count())
                                <p class="text-xs text-slate-400 mt-1">Linked: {{ $parent->linkedLearners->pluck('name')->join(', ') }}</p>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4 border-t">{{ $parents->links() }}</div>
            @else
            <x-empty-state title="No parent accounts" description="Create users with the Parent role first." />
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
