@extends('layouts.app')

@section('title', $user->name)
@section('page-title', $user->name)
@section('breadcrumb', 'Platform Admin / Users / Detail')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
    @endif

    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <h2 class="text-xl font-bold text-slate-800">{{ $user->name }}</h2>
                <x-badge :type="$user->is_active ? 'success' : 'danger'">{{ $user->is_active ? 'Active' : 'Inactive' }}</x-badge>
                <x-badge type="info">{{ $user->role?->name ?? 'No role' }}</x-badge>
                @if($user->isSuperAdmin())
                    <x-badge type="danger">Protected</x-badge>
                @endif
            </div>
            <p class="text-sm text-slate-500 mt-1">{{ $user->email }} @if($user->phone)· {{ $user->phone }}@endif</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('platform.users.index') }}" class="panel-btn-secondary text-sm">← All users</a>
            <a href="{{ route('platform.users.edit', $user) }}" class="panel-btn-primary text-sm">Edit</a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-stat-card title="Orders" :value="number_format($ordersSummary['total'])" :trend="number_format($ordersSummary['paid']).' paid'" />
        <x-stat-card title="Spend" :value="'₹'.number_format($ordersSummary['revenue'], 0)" />
        <x-stat-card title="Enrollments" :value="number_format($enrollmentsSummary['total'])" :trend="number_format($enrollmentsSummary['active']).' active'" />
        <x-stat-card title="Sessions" :value="number_format($activeSessions)" :trend="$devices->whereNull('revoked_at')->count().' devices'" />
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            <div class="glass-card rounded-2xl p-6 space-y-4">
                <h3 class="text-lg font-bold text-slate-800">Profile</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-slate-500">Name</dt><dd class="font-medium text-slate-800">{{ $user->name }}</dd></div>
                    <div><dt class="text-slate-500">Email</dt><dd class="font-medium text-slate-800">{{ $user->email }}</dd></div>
                    <div><dt class="text-slate-500">Phone</dt><dd class="font-medium text-slate-800">{{ $user->phone ?: '—' }}</dd></div>
                    <div><dt class="text-slate-500">Role</dt><dd class="font-medium text-slate-800 capitalize">{{ str_replace('-', ' ', $user->role?->slug ?? '—') }}</dd></div>
                    <div><dt class="text-slate-500">Last login</dt><dd class="font-medium text-slate-800">{{ $user->last_login_at?->format('M d, Y H:i') ?? 'Never' }}</dd></div>
                    <div><dt class="text-slate-500">Joined</dt><dd class="font-medium text-slate-800">{{ $user->created_at?->format('M d, Y') }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-slate-500">Notes</dt><dd class="text-slate-700 whitespace-pre-line">{{ $user->notes ?: '—' }}</dd></div>
                </dl>
            </div>

            <div class="glass-card rounded-2xl p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Linked institutes</h3>
                @forelse($institutes as $institute)
                    <a href="{{ route('platform.companies.show', $institute) }}" class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0 hover:bg-slate-50 rounded-lg px-2 -mx-2">
                        <div>
                            <p class="text-sm font-semibold text-slate-800">{{ $institute->name }}</p>
                            <p class="text-xs text-slate-400">{{ $institute->subscriptionPackage?->name ?? 'No package' }} · {{ $institute->is_active ? 'Active' : 'Suspended' }}</p>
                        </div>
                        <span class="text-xs text-indigo-600 font-medium">Open →</span>
                    </a>
                @empty
                    <p class="text-sm text-slate-500">No linked institute.</p>
                @endforelse
            </div>

            <div class="glass-card rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-slate-800">Activity timeline</h3>
                    <a href="{{ route('platform.activity.index', ['search' => $user->email]) }}" class="text-sm text-indigo-600 font-medium">Logs →</a>
                </div>
                <div class="space-y-3 max-h-[28rem] overflow-y-auto">
                    @forelse($activity as $log)
                        <div class="flex items-start justify-between gap-3 py-2 border-b border-slate-100 last:border-0">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-slate-800">{{ $log->description ?? ucfirst($log->action) }}</p>
                                <p class="text-xs text-slate-500">{{ $log->created_at->format('M d, Y H:i') }} · {{ $log->user?->name ?? 'System' }}</p>
                            </div>
                            <x-badge type="info">{{ ucfirst(str_replace('_', ' ', (string) $log->action)) }}</x-badge>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No activity yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="glass-card rounded-2xl p-6 space-y-4">
                <h3 class="text-lg font-bold text-slate-800">Actions</h3>

                <form method="POST" action="{{ route('platform.users.toggle-active', $user) }}">
                    @csrf
                    <button class="w-full panel-btn-secondary text-sm {{ $user->is_active ? 'text-red-600' : 'text-emerald-700' }}">
                        {{ $user->is_active ? 'Deactivate user' : 'Activate user' }}
                    </button>
                </form>

                <form method="POST" action="{{ route('platform.users.role', $user) }}" class="space-y-2 pt-2 border-t border-slate-100">
                    @csrf
                    <label class="block text-xs font-medium text-slate-500">Change role</label>
                    <select name="role_id" class="panel-input w-full">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" @selected((int) $user->role_id === (int) $role->id)>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <button class="w-full panel-btn-primary text-sm">Update role</button>
                    @if($user->isSuperAdmin())
                        <p class="text-[11px] text-amber-700">Super-admin demotion is blocked if this is the last active super-admin, or for your own account.</p>
                    @endif
                </form>

                <form method="POST" action="{{ route('platform.users.reset-password', $user) }}" class="space-y-2 pt-2 border-t border-slate-100">
                    @csrf
                    <label class="block text-xs font-medium text-slate-500">Reset password</label>
                    <input type="password" name="password" class="panel-input w-full" placeholder="New password" required>
                    <input type="password" name="password_confirmation" class="panel-input w-full" placeholder="Confirm password" required>
                    <button class="w-full panel-btn-secondary text-sm">Reset password</button>
                </form>

                @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('platform.users.force-logout', $user) }}" class="pt-2 border-t border-slate-100">
                        @csrf
                        <button class="w-full panel-btn-secondary text-sm text-amber-700">Force logout / revoke sessions</button>
                    </form>
                @endif
            </div>

            <div class="glass-card rounded-2xl p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Devices</h3>
                @forelse($devices as $device)
                    <div class="flex items-center justify-between gap-2 py-2 border-b border-slate-100 last:border-0 text-sm">
                        <div>
                            <p class="font-medium text-slate-800">{{ $device->device_name ?: 'Browser' }}</p>
                            <p class="text-xs text-slate-400">{{ $device->ip_address }} · {{ $device->last_seen_at?->diffForHumans() ?? '—' }}</p>
                            @if($device->revoked_at)<x-badge type="danger">Revoked</x-badge>@endif
                        </div>
                        @if(! $device->revoked_at)
                            <form method="POST" action="{{ route('platform.users.devices.revoke', [$user, $device]) }}">
                                @csrf
                                <button class="text-xs text-red-500">Revoke</button>
                            </form>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No devices recorded.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
