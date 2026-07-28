@extends('layouts.app')

@section('title', 'Referral Program')
@section('page-title', 'Referral Program')
@section('breadcrumb', 'Sales / Referrals')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-sm text-slate-500">Unique learner codes, attribution, and wallet rewards.</p>
        <a href="{{ route('admin.referrals.settings') }}" class="panel-btn-secondary">Reward Settings</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card title="Codes" :value="number_format($stats['codes'])" />
        <x-stat-card title="Referrals" :value="number_format($stats['total'])" />
        <x-stat-card title="Rewarded" :value="number_format($stats['rewarded'])" />
        <x-stat-card title="Pending" :value="number_format($stats['pending'])" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-lg font-bold text-slate-800">Generate / Ensure Code</h3>
            <form method="POST" action="{{ route('admin.referrals.codes.store') }}" class="space-y-3">
                @csrf
                <x-form-input label="Learner" name="user_id" type="select" required>
                    <option value="">Select learner</option>
                    @foreach($learners as $learner)
                        <option value="{{ $learner->id }}">{{ $learner->name }} ({{ $learner->email }})</option>
                    @endforeach
                </x-form-input>
                <x-form-input label="Custom Code (optional)" name="code" :value="old('code')" />
                <x-form-input label="Max Uses (optional)" name="max_uses" type="number" :value="old('max_uses')" />
                <button type="submit" class="panel-btn-primary">Save Code</button>
            </form>
        </div>

        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-lg font-bold text-slate-800">Apply Referral Code</h3>
            <form method="POST" action="{{ route('admin.referrals.apply') }}" class="space-y-3">
                @csrf
                <x-form-input label="New / Referred Learner" name="referred_id" type="select" required>
                    <option value="">Select learner</option>
                    @foreach($learners as $learner)
                        <option value="{{ $learner->id }}">{{ $learner->name }} ({{ $learner->email }})</option>
                    @endforeach
                </x-form-input>
                <x-form-input label="Referral Code" name="referral_code" :value="old('referral_code')" required />
                <button type="submit" class="panel-btn-primary">Apply & Reward</button>
            </form>
        </div>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800">Referral Codes</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead>
                    <tr class="text-left text-slate-500">
                        <th class="px-6 py-3">Learner</th>
                        <th class="px-6 py-3">Code</th>
                        <th class="px-6 py-3">Uses</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($codes as $code)
                    <tr>
                        <td class="px-6 py-3">{{ $code->user?->name }}</td>
                        <td class="px-6 py-3 font-mono text-emerald-700">{{ $code->code }}</td>
                        <td class="px-6 py-3">{{ $code->uses_count }}@if($code->max_uses)/{{ $code->max_uses }}@endif ({{ $code->referrals_count }} referrals)</td>
                        <td class="px-6 py-3"><x-badge :type="$code->is_active ? 'success' : 'danger'">{{ $code->is_active ? 'Active' : 'Inactive' }}</x-badge></td>
                        <td class="px-6 py-3">
                            <form method="POST" action="{{ route('admin.referrals.codes.toggle', $code) }}">@csrf
                                <button class="text-sm text-emerald-700 hover:underline">{{ $code->is_active ? 'Deactivate' : 'Activate' }}</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">No referral codes yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">{{ $codes->links() }}</div>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="text-lg font-bold text-slate-800">Referral Report</h3>
            <form method="GET" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <select name="status" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    <option value="">All</option>
                    @foreach(['pending','qualified','rewarded','rejected'] as $st)
                        <option value="{{ $st }}" @selected(request('status')===$st)>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
                <button class="panel-btn-secondary">Filter</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead>
                    <tr class="text-left text-slate-500">
                        <th class="px-6 py-3">Referrer</th>
                        <th class="px-6 py-3">Referred</th>
                        <th class="px-6 py-3">Code</th>
                        <th class="px-6 py-3">Rewards</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($referrals as $referral)
                    <tr>
                        <td class="px-6 py-3">{{ $referral->referrer?->name }}</td>
                        <td class="px-6 py-3">{{ $referral->referred?->name }}</td>
                        <td class="px-6 py-3 font-mono text-emerald-700">{{ $referral->referralCode?->code }}</td>
                        <td class="px-6 py-3">₹{{ number_format($referral->referrer_reward, 0) }} / ₹{{ number_format($referral->referred_reward, 0) }}</td>
                        <td class="px-6 py-3"><x-badge :type="match($referral->status){'rewarded'=>'success','pending'=>'warning','qualified'=>'info',default=>'danger'}">{{ ucfirst($referral->status) }}</x-badge></td>
                        <td class="px-6 py-3 text-slate-500">{{ $referral->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-3">
                            @if($referral->status !== 'rewarded')
                            <form method="POST" action="{{ route('admin.referrals.reward', $referral) }}">@csrf
                                <button class="text-sm text-emerald-700 hover:underline">Reward</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-8 text-center text-slate-500">No referrals recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">{{ $referrals->links() }}</div>
    </div>
</div>
@endsection
