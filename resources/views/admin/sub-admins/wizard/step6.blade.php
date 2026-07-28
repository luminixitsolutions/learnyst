@extends('layouts.app')

@section('title', 'Sub Admin Wizard — Preview')
@section('page-title', 'Create Sub Admin')
@section('breadcrumb', 'Wizard / Step 6 — Preview')

@section('content')
<div class="max-w-3xl mx-auto">
    @include('admin.sub-admins.wizard._progress', ['current' => 6])

    <div class="glass-card rounded-2xl p-6 space-y-6">
        <h3 class="text-lg font-bold text-slate-800">Review & Confirm</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                <p class="text-xs text-slate-500 uppercase tracking-wide mb-2">Details</p>
                <p class="text-slate-800 font-semibold">{{ $data['details']['name'] ?? '—' }}</p>
                <p class="text-sm text-slate-500">{{ $data['details']['email'] ?? '—' }}</p>
                @if(!empty($data['details']['phone']))
                    <p class="text-sm text-slate-500">{{ $data['details']['phone'] }}</p>
                @endif
                @if(!empty($data['details']['expertise']))
                    <p class="text-sm font-medium text-indigo-600 mt-1">{{ $data['details']['expertise'] }}</p>
                @endif
                @if(!empty($data['details']['bio']))
                    <p class="text-xs text-slate-500 mt-2 whitespace-pre-line">{{ \Illuminate\Support\Str::limit($data['details']['bio'], 180) }}</p>
                @endif
            </div>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                <p class="text-xs text-slate-500 uppercase tracking-wide mb-2">Role</p>
                @php $role = \App\Models\Role::find($data['role_id'] ?? null); @endphp
                <p class="text-slate-800">{{ $role?->name ?? '—' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                <p class="text-xs text-slate-500 uppercase tracking-wide mb-2">Courses</p>
                @php $courseCount = count($data['course_ids'] ?? []); @endphp
                <p class="text-2xl font-bold text-slate-800">{{ $courseCount ?: 'All' }}</p>
                <p class="text-xs text-slate-500">{{ $courseCount ? 'scoped courses' : 'full access' }}</p>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                <p class="text-xs text-slate-500 uppercase tracking-wide mb-2">Bundles</p>
                @php $bundleCount = count($data['bundle_ids'] ?? []); @endphp
                <p class="text-2xl font-bold text-slate-800">{{ $bundleCount ?: 'All' }}</p>
                <p class="text-xs text-slate-500">{{ $bundleCount ? 'scoped bundles' : 'full access' }}</p>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                <p class="text-xs text-slate-500 uppercase tracking-wide mb-2">Communities</p>
                @php $communityCount = count($data['community_ids'] ?? []); @endphp
                <p class="text-2xl font-bold text-slate-800">{{ $communityCount ?: 'All' }}</p>
                <p class="text-xs text-slate-500">{{ $communityCount ? 'scoped communities' : 'full access' }}</p>
            </div>
        </div>

        @if(empty($data['details']) || empty($data['role_id']))
        <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm">
            Please complete all required steps before finishing.
        </div>
        @endif

        <div class="flex justify-between pt-4 border-t border-slate-200">
            <a href="{{ route('admin.sub-admins.wizard.step', 5) }}" class="text-sm text-slate-500 hover:text-white">← Back</a>
            <form method="POST" action="{{ route('admin.sub-admins.wizard.finish') }}">
                @csrf
                <button type="submit" @disabled(empty($data['details']) || empty($data['role_id']))
                        class="px-5 py-2.5 rounded-xl panel-btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                    Create Sub Admin
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
