<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Company;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PlatformDashboardController extends Controller
{
    public function index()
    {
        $todayStart = now()->startOfDay();
        $since30 = now()->subDays(29)->startOfDay();

        $learnerRoleId = Role::where('slug', 'learner')->value('id');

        $institutesTotal = Company::count();
        $institutesPublic = Company::where('is_public', true)->count();
        $institutesHidden = max(0, $institutesTotal - $institutesPublic);

        $usersByRole = Role::query()
            ->orderBy('name')
            ->withCount('users')
            ->get()
            ->mapWithKeys(fn (Role $role) => [$role->slug => [
                'name' => $role->name,
                'count' => (int) $role->users_count,
            ]])
            ->all();

        $stats = [
            'institutes_total' => $institutesTotal,
            'institutes_public' => $institutesPublic,
            'institutes_hidden' => $institutesHidden,
            'users_total' => User::count(),
            'users_by_role' => $usersByRole,
            'courses_total' => Course::count(),
            'enrollments_total' => CourseEnrollment::count(),
            'platform_revenue' => (float) Order::where('payment_status', 'paid')->sum('total'),
            'today_logins' => User::where('last_login_at', '>=', $todayStart)->count(),
            'today_orders' => Order::where('created_at', '>=', $todayStart)->count(),
            'failed_payments' => Payment::where('status', 'failed')->count(),
            'failed_payments_today' => Payment::where('status', 'failed')
                ->where('created_at', '>=', $todayStart)
                ->count(),
            'failed_orders' => Order::where('payment_status', 'failed')->count(),
            'active_learners' => $learnerRoleId
                ? User::where('role_id', $learnerRoleId)->where('is_active', true)->count()
                : 0,
        ];

        $revenueTrend = $this->dailySeries(
            Order::query()
                ->where('payment_status', 'paid')
                ->where('created_at', '>=', $since30)
                ->selectRaw('DATE(created_at) as day, SUM(total) as value')
                ->groupBy('day')
                ->pluck('value', 'day'),
            $since30,
            asFloat: true
        );

        $institutesTrend = $this->dailySeries(
            Company::query()
                ->where('created_at', '>=', $since30)
                ->selectRaw('DATE(created_at) as day, COUNT(*) as value')
                ->groupBy('day')
                ->pluck('value', 'day'),
            $since30
        );

        $learnersTrend = $this->dailySeries(
            User::query()
                ->when($learnerRoleId, fn ($q) => $q->where('role_id', $learnerRoleId))
                ->where('created_at', '>=', $since30)
                ->selectRaw('DATE(created_at) as day, COUNT(*) as value')
                ->groupBy('day')
                ->pluck('value', 'day'),
            $since30
        );

        $alerts = $this->buildAlerts($stats);

        $recentActivity = ActivityLog::with('user')
            ->latest()
            ->take(20)
            ->get();

        $quickLinks = [
            ['label' => 'Institutes', 'route' => 'platform.companies.index', 'hint' => 'Manage tenants'],
            ['label' => 'Users', 'route' => 'platform.users.index', 'hint' => 'Platform accounts'],
            ['label' => 'Activity', 'route' => 'platform.activity.index', 'hint' => 'Audit trail'],
            ['label' => 'Settings', 'route' => 'platform.settings.index', 'hint' => 'Platform config'],
            ['label' => 'Packages', 'route' => 'platform.subscription-packages.index', 'hint' => 'Pricing plans'],
        ];

        return view('platform.dashboard', compact(
            'stats',
            'revenueTrend',
            'institutesTrend',
            'learnersTrend',
            'alerts',
            'recentActivity',
            'quickLinks'
        ));
    }

    /**
     * Fill a continuous 30-day series keyed by Y-m-d.
     *
     * @param  Collection<string, mixed>  $raw
     * @return Collection<string, float|int>
     */
    protected function dailySeries(Collection $raw, Carbon $since, bool $asFloat = false): Collection
    {
        $normalized = $raw->mapWithKeys(function ($value, $key) {
            $day = Carbon::parse((string) $key)->toDateString();

            return [$day => $value];
        });

        $series = collect();
        $cursor = $since->copy();
        $end = now()->startOfDay();

        while ($cursor->lte($end)) {
            $key = $cursor->toDateString();
            $value = $normalized->get($key, 0);
            $series->put($key, $asFloat ? (float) $value : (int) $value);
            $cursor->addDay();
        }

        return $series;
    }

    protected function buildAlerts(array $stats): array
    {
        $alerts = [];

        if (($stats['failed_payments_today'] ?? 0) > 0) {
            $alerts[] = [
                'level' => 'danger',
                'title' => 'Failed payments today',
                'body' => number_format($stats['failed_payments_today']).' payment(s) failed today.',
            ];
        } elseif (($stats['failed_payments'] ?? 0) > 0) {
            $alerts[] = [
                'level' => 'warning',
                'title' => 'Failed payments on record',
                'body' => number_format($stats['failed_payments']).' failed payment(s) overall.',
            ];
        }

        if (($stats['institutes_hidden'] ?? 0) > 0) {
            $alerts[] = [
                'level' => 'warning',
                'title' => 'Hidden institutes',
                'body' => number_format($stats['institutes_hidden']).' institute(s) are not publicly listed.',
                'href' => route('platform.companies.index'),
            ];
        }

        $inactiveOwners = Company::query()
            ->whereHas('owner', fn ($q) => $q->where('is_active', false))
            ->count();

        if ($inactiveOwners > 0) {
            $alerts[] = [
                'level' => 'danger',
                'title' => 'Inactive institute owners',
                'body' => number_format($inactiveOwners).' institute(s) have a deactivated owner account.',
                'href' => route('platform.companies.index'),
            ];
        }

        $criticalActions = ActivityLog::query()
            ->where('created_at', '>=', now()->subDays(7))
            ->where(function ($q) {
                $q->whereIn('action', [
                    'delete', 'deleted', 'destroy', 'force_delete',
                    'impersonate', 'blocked', 'logout_forced',
                    'device_revoked', 'integration_updated',
                ])->orWhere('action', 'like', '%delete%')
                    ->orWhere('action', 'like', '%fail%')
                    ->orWhere('description', 'like', '%deactivated%')
                    ->orWhere('description', 'like', '%failed%');
            })
            ->latest()
            ->take(5)
            ->get();

        foreach ($criticalActions as $log) {
            $alerts[] = [
                'level' => 'info',
                'title' => 'Recent critical action',
                'body' => ($log->description ?: ucfirst((string) $log->action))
                    .' — '.($log->user?->name ?? 'System')
                    .' · '.$log->created_at->diffForHumans(),
                'href' => route('platform.activity.index'),
            ];
        }

        return $alerts;
    }
}
