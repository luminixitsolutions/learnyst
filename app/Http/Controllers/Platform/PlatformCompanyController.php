<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Company;
use App\Models\Course;
use App\Models\Order;
use App\Models\Role;
use App\Models\SubscriptionPackage;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\CompanyService;
use App\Services\PlatformImpersonationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class PlatformCompanyController extends Controller
{
    public function index(Request $request)
    {
        CompanyService::syncMissingCompanies();

        $packages = SubscriptionPackage::query()->ordered()->get(['id', 'name']);

        $query = Company::query()
            ->with(['owner.role', 'subscriptionPackage'])
            ->withCount(['publishedCourses as courses_count']);

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhereHas('owner', function ($owner) use ($search) {
                        $owner->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        match ($request->query('status')) {
            'active' => $query->where('is_active', true),
            'suspended' => $query->where('is_active', false),
            'public' => $query->where('is_public', true)->where('is_active', true),
            'hidden' => $query->where('is_public', false),
            default => null,
        };

        if ($request->filled('package')) {
            if ($request->query('package') === 'none') {
                $query->whereNull('subscription_package_id');
            } else {
                $query->where('subscription_package_id', (int) $request->query('package'));
            }
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->query('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->query('to'));
        }

        $companies = $query->orderBy('name')->paginate(20)->withQueryString();

        $stats = [
            'total' => Company::count(),
            'active' => Company::where('is_active', true)->count(),
            'suspended' => Company::where('is_active', false)->count(),
            'public' => Company::where('is_public', true)->where('is_active', true)->count(),
            'courses' => Course::count(),
        ];

        return view('platform.companies.index', compact('companies', 'stats', 'packages'));
    }

    public function create()
    {
        $packages = SubscriptionPackage::query()->active()->ordered()->get();

        return view('platform.companies.create', compact('packages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'institute_name' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'city' => ['nullable', 'string', 'max:120'],
            'is_public' => ['boolean'],
            'subscription_package_id' => ['nullable', 'integer', 'exists:subscription_packages,id'],
            'owner_name' => ['required', 'string', 'max:255'],
            'owner_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'owner_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $adminRoleId = Role::where('slug', 'admin')->value('id');
        abort_unless($adminRoleId, 500, 'Admin role missing.');

        $company = DB::transaction(function () use ($validated, $adminRoleId, $request) {
            $owner = User::create([
                'name' => $validated['owner_name'],
                'email' => $validated['owner_email'],
                'password' => Hash::make($validated['owner_password']),
                'role_id' => $adminRoleId,
                'phone' => $validated['phone'] ?? null,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            return Company::create([
                'owner_user_id' => $owner->id,
                'name' => $validated['institute_name'],
                'slug' => Company::uniqueSlug($validated['institute_name']),
                'tagline' => $validated['tagline'] ?? null,
                'email' => $validated['email'] ?? $owner->email,
                'phone' => $validated['phone'] ?? null,
                'city' => $validated['city'] ?? null,
                'is_public' => $request->boolean('is_public', true),
                'is_active' => true,
                'subscription_package_id' => $validated['subscription_package_id'] ?? null,
                'package_assigned_at' => ! empty($validated['subscription_package_id']) ? now() : null,
            ]);
        });

        ActivityLogger::log('institute_created', "Institute created: {$company->name}", $company, [
            'owner_email' => $validated['owner_email'],
            'package_id' => $company->subscription_package_id,
        ]);

        return redirect()
            ->route('platform.companies.show', $company)
            ->with('success', 'Institute created with owner admin account.');
    }

    public function show(Company $company)
    {
        $company->load(['owner.role', 'subscriptionPackage']);

        $ownerId = (int) $company->owner_user_id;
        $courseIds = Course::where('created_by', $ownerId)->pluck('id');

        $stats = [
            'users' => User::where(function ($q) use ($ownerId) {
                $q->where('id', $ownerId)->orWhere('created_by', $ownerId);
            })->count(),
            'courses' => $courseIds->count(),
            'published_courses' => Course::where('created_by', $ownerId)->published()->count(),
            'revenue' => (float) Order::query()
                ->where('payment_status', 'paid')
                ->whereHas('items', fn ($q) => $q->whereIn('course_id', $courseIds))
                ->sum('total'),
            'orders' => Order::query()
                ->whereHas('items', fn ($q) => $q->whereIn('course_id', $courseIds))
                ->count(),
        ];

        $recentActivity = ActivityLog::with('user')
            ->where(function ($q) use ($company, $ownerId) {
                $q->where(function ($inner) use ($company) {
                    $inner->where('subject_type', Company::class)
                        ->where('subject_id', $company->id);
                })->orWhere('user_id', $ownerId)
                    ->orWhere('description', 'like', '%'.$company->name.'%');
            })
            ->latest()
            ->take(15)
            ->get();

        $packages = SubscriptionPackage::query()->ordered()->get();

        return view('platform.companies.show', compact('company', 'stats', 'recentActivity', 'packages'));
    }

    public function edit(Company $company)
    {
        $company->load(['owner', 'subscriptionPackage']);
        $packages = SubscriptionPackage::query()->ordered()->get();

        return view('platform.companies.edit', compact('company', 'packages'));
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('companies', 'slug')->ignore($company->id)],
            'tagline' => ['nullable', 'string', 'max:255'],
            'about' => ['nullable', 'string'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:500'],
            'subscription_package_id' => ['nullable', 'integer', 'exists:subscription_packages,id'],
            'is_public' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        $slug = filled($validated['slug'] ?? null)
            ? $validated['slug']
            : Company::uniqueSlug($validated['name'], $company->id);

        $packageId = $validated['subscription_package_id'] ?? null;
        $packageChanged = (int) ($company->subscription_package_id ?? 0) !== (int) ($packageId ?? 0);

        $company->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'tagline' => $validated['tagline'] ?? null,
            'about' => $validated['about'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'website_url' => $validated['website_url'] ?? null,
            'city' => $validated['city'] ?? null,
            'address' => $validated['address'] ?? null,
            'subscription_package_id' => $packageId,
            'package_assigned_at' => $packageChanged
                ? ($packageId ? now() : null)
                : $company->package_assigned_at,
            'is_public' => $request->boolean('is_public'),
            'is_active' => $request->boolean('is_active'),
        ]);

        ActivityLogger::log('institute_updated', "Institute profile updated: {$company->name}", $company, [
            'package_changed' => $packageChanged,
        ]);

        return redirect()
            ->route('platform.companies.show', $company)
            ->with('success', 'Institute updated.');
    }

    public function toggleActive(Company $company)
    {
        $company->update(['is_active' => ! $company->is_active]);

        $state = $company->is_active ? 'activated' : 'suspended';
        ActivityLogger::log(
            'institute_'.$state,
            "Institute {$state}: {$company->name}",
            $company
        );

        return back()->with('success', "Institute {$state}.");
    }

    public function togglePublic(Company $company)
    {
        $company->update(['is_public' => ! $company->is_public]);

        $state = $company->is_public ? 'public' : 'hidden';
        ActivityLogger::log(
            'institute_visibility_changed',
            "Institute set to {$state}: {$company->name}",
            $company
        );

        return back()->with('success', "Institute is now {$state}.");
    }

    public function assignPackage(Request $request, Company $company)
    {
        $validated = $request->validate([
            'subscription_package_id' => ['nullable', 'integer', 'exists:subscription_packages,id'],
        ]);

        $packageId = $validated['subscription_package_id'] ?? null;
        $company->update([
            'subscription_package_id' => $packageId,
            'package_assigned_at' => $packageId ? now() : null,
        ]);

        $label = $packageId
            ? (SubscriptionPackage::find($packageId)?->name ?? 'package')
            : 'none';

        ActivityLogger::log(
            'institute_package_assigned',
            "Package assigned ({$label}) to {$company->name}",
            $company,
            ['subscription_package_id' => $packageId]
        );

        return back()->with('success', 'Subscription package updated.');
    }

    public function enterPanel(Request $request, Company $company)
    {
        PlatformImpersonationService::enter($company);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', "You are now viewing {$company->name}'s institute panel.");
    }

    public function exitPanel(Request $request)
    {
        PlatformImpersonationService::exit();

        return redirect()
            ->route('platform.companies.index')
            ->with('success', 'Returned to platform admin.');
    }
}
