<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Company;
use App\Models\CourseEnrollment;
use App\Models\LoginDevice;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class PlatformUserController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::orderBy('name')->get();
        $institutes = Company::query()->orderBy('name')->get(['id', 'name', 'owner_user_id']);

        $query = User::query()->with(['role', 'company', 'creator.company']);

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('role', fn ($q) => $q->where('slug', $request->query('role')));
        }

        match ($request->query('status')) {
            'active' => $query->where('is_active', true),
            'inactive' => $query->where('is_active', false),
            default => null,
        };

        if ($request->filled('institute')) {
            $ownerId = Company::where('id', (int) $request->query('institute'))->value('owner_user_id');
            if ($ownerId) {
                $query->where(function ($q) use ($ownerId) {
                    $q->where('id', $ownerId)
                        ->orWhere('created_by', $ownerId);
                });
            }
        }

        match ($request->query('last_login')) {
            'today' => $query->where('last_login_at', '>=', now()->startOfDay()),
            '7d' => $query->where('last_login_at', '>=', now()->subDays(7)),
            '30d' => $query->where('last_login_at', '>=', now()->subDays(30)),
            'never' => $query->whereNull('last_login_at'),
            default => null,
        };

        $users = $query->latest()->paginate(30)->withQueryString();

        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'super_admins' => User::whereHas('role', fn ($q) => $q->where('slug', 'super-admin'))->count(),
        ];

        return view('platform.users.index', compact('users', 'roles', 'institutes', 'stats'));
    }

    public function create()
    {
        $roles = $this->assignableRoles();
        $institutes = Company::query()->with('owner')->orderBy('name')->get();

        return view('platform.users.create', compact('roles', 'institutes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:40'],
            'role_id' => ['required', 'integer', Rule::in($this->assignableRoles()->pluck('id')->all())],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'is_active' => ['boolean'],
        ]);

        $role = Role::findOrFail($validated['role_id']);
        if ($role->slug === 'super-admin') {
            // Only allow creating another super-admin explicitly via assignableRoles (included)
        }

        $createdBy = null;
        if (! empty($validated['company_id'])) {
            $createdBy = Company::where('id', $validated['company_id'])->value('owner_user_id');
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role_id' => $validated['role_id'],
            'created_by' => $role->slug === 'super-admin' ? null : $createdBy,
            'password' => $validated['password'],
            'is_active' => $request->boolean('is_active', true),
            'email_verified_at' => now(),
        ]);

        ActivityLogger::log('user_created', "User created: {$user->email} ({$role->slug})", $user);

        return redirect()
            ->route('platform.users.show', $user)
            ->with('success', 'User created.');
    }

    public function show(User $user)
    {
        $user->load(['role', 'company.subscriptionPackage', 'creator.company']);

        $institutes = $this->linkedInstitutes($user);

        $ordersSummary = [
            'total' => Order::where('user_id', $user->id)->count(),
            'paid' => Order::where('user_id', $user->id)->where('payment_status', 'paid')->count(),
            'revenue' => (float) Order::where('user_id', $user->id)->where('payment_status', 'paid')->sum('total'),
        ];

        $enrollmentsSummary = [
            'total' => CourseEnrollment::where('user_id', $user->id)->count(),
            'active' => CourseEnrollment::where('user_id', $user->id)->where('status', 'active')->count(),
            'completed' => CourseEnrollment::where('user_id', $user->id)->where('status', 'completed')->count(),
        ];

        $activity = ActivityLog::with('user')
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere(function ($inner) use ($user) {
                        $inner->where('subject_type', User::class)->where('subject_id', $user->id);
                    });
            })
            ->latest()
            ->take(25)
            ->get();

        $devices = LoginDevice::where('user_id', $user->id)
            ->latest('last_seen_at')
            ->limit(10)
            ->get();

        $activeSessions = 0;
        if (Schema::hasTable('sessions') && Schema::hasColumn('sessions', 'user_id')) {
            $activeSessions = DB::table('sessions')->where('user_id', $user->id)->count();
        }

        $roles = $this->assignableRoles(includeCurrent: $user);

        return view('platform.users.show', compact(
            'user',
            'institutes',
            'ordersSummary',
            'enrollmentsSummary',
            'activity',
            'devices',
            'activeSessions',
            'roles'
        ));
    }

    public function edit(User $user)
    {
        $this->guardProtectedTarget($user, allowSelfEdit: true);

        $roles = $this->assignableRoles(includeCurrent: $user);
        $institutes = Company::query()->orderBy('name')->get();
        $currentInstituteId = $user->company?->id
            ?? Company::where('owner_user_id', $user->created_by)->value('id');

        return view('platform.users.edit', compact('user', 'roles', 'institutes', 'currentInstituteId'));
    }

    public function update(Request $request, User $user)
    {
        $this->guardProtectedTarget($user, allowSelfEdit: true);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:40'],
            'role_id' => ['required', 'integer', Rule::in($this->assignableRoles(includeCurrent: $user)->pluck('id')->all())],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $newRole = Role::findOrFail($validated['role_id']);
        $this->guardRoleChange($user, $newRole);
        $this->guardStatusChange($user, $request->boolean('is_active', true));

        $oldRole = $user->role?->slug;
        $oldActive = $user->is_active;

        $createdBy = $user->created_by;
        if ($newRole->slug === 'super-admin') {
            $createdBy = null;
        } elseif ($request->filled('company_id')) {
            $createdBy = Company::where('id', $validated['company_id'])->value('owner_user_id');
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role_id' => $newRole->id,
            'created_by' => $createdBy,
            'is_active' => $request->boolean('is_active', true),
            'notes' => $validated['notes'] ?? $user->notes,
        ]);

        if ($oldRole !== $newRole->slug) {
            ActivityLogger::log(
                'user_role_changed',
                "Role changed for {$user->email}: {$oldRole} → {$newRole->slug}",
                $user,
                ['from' => $oldRole, 'to' => $newRole->slug]
            );
        }

        if ((bool) $oldActive !== (bool) $user->is_active) {
            ActivityLogger::log(
                $user->is_active ? 'user_activated' : 'user_deactivated',
                ($user->is_active ? 'Activated' : 'Deactivated')." user {$user->email}",
                $user
            );
        }

        ActivityLogger::log('user_updated', "User profile updated: {$user->email}", $user);

        return redirect()
            ->route('platform.users.show', $user)
            ->with('success', 'User updated.');
    }

    public function toggleActive(User $user)
    {
        $this->guardProtectedTarget($user);
        $this->guardStatusChange($user, ! $user->is_active);

        $user->update(['is_active' => ! $user->is_active]);

        $action = $user->is_active ? 'user_activated' : 'user_deactivated';
        ActivityLogger::log($action, ($user->is_active ? 'Activated' : 'Deactivated')." user {$user->email}", $user);

        if (! $user->is_active) {
            $this->revokeSessionsAndDevices($user);
        }

        return back()->with('success', $user->is_active ? 'User activated.' : 'User deactivated.');
    }

    public function updateRole(Request $request, User $user)
    {
        $this->guardProtectedTarget($user);

        $validated = $request->validate([
            'role_id' => ['required', 'integer', Rule::in($this->assignableRoles(includeCurrent: $user)->pluck('id')->all())],
        ]);

        $newRole = Role::findOrFail($validated['role_id']);
        $this->guardRoleChange($user, $newRole);

        $old = $user->role?->slug;
        $user->update([
            'role_id' => $newRole->id,
            'created_by' => $newRole->slug === 'super-admin' ? null : $user->created_by,
        ]);

        ActivityLogger::log(
            'user_role_changed',
            "Role changed for {$user->email}: {$old} → {$newRole->slug}",
            $user,
            ['from' => $old, 'to' => $newRole->slug]
        );

        return back()->with('success', 'Role updated.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $this->guardProtectedTarget($user, allowSelfEdit: true);

        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update(['password' => $validated['password']]);
        $this->revokeSessionsAndDevices($user);

        ActivityLogger::log('user_password_reset', "Password reset for {$user->email}", $user);

        return back()->with('success', 'Password reset. Active sessions were revoked.');
    }

    public function forceLogout(User $user)
    {
        $this->guardProtectedTarget($user, allowSelfEdit: false);

        $count = $this->revokeSessionsAndDevices($user);

        ActivityLogger::log(
            'user_force_logout',
            "Forced logout for {$user->email} ({$count} session/device records cleared)",
            $user
        );

        return back()->with('success', 'User sessions and devices revoked.');
    }

    public function revokeDevice(User $user, LoginDevice $device)
    {
        abort_unless($device->user_id === $user->id, 404);
        $this->guardProtectedTarget($user);

        $device->update(['revoked_at' => now()]);

        if ($device->session_id && Schema::hasTable('sessions')) {
            DB::table('sessions')->where('id', $device->session_id)->delete();
        }

        ActivityLogger::log('user_device_revoked', "Device revoked for {$user->email}", $user, [
            'device_id' => $device->device_id,
        ]);

        return back()->with('success', 'Device revoked.');
    }

    /**
     * Roles that platform admin may assign. Super-admin included for platform ops.
     */
    protected function assignableRoles(?User $includeCurrent = null)
    {
        $roles = Role::query()->orderBy('name')->get();

        // Always keep current role available when editing, even if filtered later
        return $roles;
    }

    protected function linkedInstitutes(User $user)
    {
        $institutes = collect();

        if ($user->company) {
            $institutes->push($user->company);
        }

        if ($user->created_by) {
            $parentCompany = Company::with('subscriptionPackage')
                ->where('owner_user_id', $user->created_by)
                ->first();
            if ($parentCompany) {
                $institutes->push($parentCompany);
            }
        }

        // Owned staff under this user (institute owner)
        $owned = Company::where('owner_user_id', $user->id)->get();
        $institutes = $institutes->merge($owned);

        return $institutes->unique('id')->values();
    }

    protected function guardProtectedTarget(User $user, bool $allowSelfEdit = false): void
    {
        if ($user->id === Auth::id() && ! $allowSelfEdit) {
            throw ValidationException::withMessages([
                'user' => 'You cannot perform this action on your own account.',
            ]);
        }
    }

    protected function guardRoleChange(User $user, Role $newRole): void
    {
        $currentSlug = $user->role?->slug;

        if ($currentSlug === 'super-admin' && $newRole->slug !== 'super-admin') {
            if ($user->id === Auth::id()) {
                throw ValidationException::withMessages([
                    'role_id' => 'You cannot demote your own super-admin account.',
                ]);
            }

            $otherActive = User::query()
                ->where('id', '!=', $user->id)
                ->where('is_active', true)
                ->whereHas('role', fn ($q) => $q->where('slug', 'super-admin'))
                ->exists();

            if (! $otherActive) {
                throw ValidationException::withMessages([
                    'role_id' => 'Cannot demote the last active super-admin.',
                ]);
            }
        }
    }

    protected function guardStatusChange(User $user, bool $willBeActive): void
    {
        if ($willBeActive || $user->role?->slug !== 'super-admin') {
            return;
        }

        if ($user->id === Auth::id()) {
            throw ValidationException::withMessages([
                'is_active' => 'You cannot deactivate your own super-admin account.',
            ]);
        }

        $otherActive = User::query()
            ->where('id', '!=', $user->id)
            ->where('is_active', true)
            ->whereHas('role', fn ($q) => $q->where('slug', 'super-admin'))
            ->exists();

        if (! $otherActive) {
            throw ValidationException::withMessages([
                'is_active' => 'Cannot deactivate the last active super-admin.',
            ]);
        }
    }

    protected function revokeSessionsAndDevices(User $user): int
    {
        $count = 0;

        if (Schema::hasTable('sessions') && Schema::hasColumn('sessions', 'user_id')) {
            $count += DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        $count += LoginDevice::where('user_id', $user->id)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now()]);

        return $count;
    }
}
