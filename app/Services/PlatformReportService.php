<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PlatformReportService
{
    public function resolveDateRange(?string $from, ?string $to, int $defaultDays = 30): array
    {
        $end = $to ? Carbon::parse($to)->endOfDay() : now()->endOfDay();
        $start = $from
            ? Carbon::parse($from)->startOfDay()
            : $end->copy()->subDays($defaultDays - 1)->startOfDay();

        if ($start->gt($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        return [$start, $end];
    }

    /**
     * @return Collection<int, array{company: Company, revenue: float, orders: int, learners: int, courses: int, enrollments: int}>
     */
    public function institutePerformance(?Carbon $from = null, ?Carbon $to = null): Collection
    {
        $learnerRoleId = Role::where('slug', 'learner')->value('id');

        return Company::query()
            ->with('subscriptionPackage')
            ->orderBy('name')
            ->get()
            ->map(function (Company $company) use ($from, $to, $learnerRoleId) {
                $ownerId = $company->owner_user_id;

                $orders = Order::query()
                    ->where('payment_status', 'paid')
                    ->where(function ($q) use ($ownerId) {
                        $q->whereHas('items.course', fn ($c) => $c->where('created_by', $ownerId))
                            ->orWhereHas('user', fn ($u) => $u->where('created_by', $ownerId)->orWhere('id', $ownerId));
                    });
                if ($from) {
                    $orders->whereRaw('DATE(COALESCE(paid_at, created_at)) >= ?', [$from->toDateString()]);
                }
                if ($to) {
                    $orders->whereRaw('DATE(COALESCE(paid_at, created_at)) <= ?', [$to->toDateString()]);
                }

                $learnersQuery = User::query()->where('created_by', $ownerId);
                if ($learnerRoleId) {
                    $learnersQuery->where('role_id', $learnerRoleId);
                }

                $coursesQuery = Course::query()->where('created_by', $ownerId);

                $enrollmentsQuery = CourseEnrollment::query()
                    ->whereHas('course', fn ($c) => $c->where('created_by', $ownerId));
                if ($from) {
                    $enrollmentsQuery->where('enrolled_at', '>=', $from);
                }
                if ($to) {
                    $enrollmentsQuery->where('enrolled_at', '<=', $to);
                }

                return [
                    'company' => $company,
                    'revenue' => (float) (clone $orders)->sum('total'),
                    'orders' => (clone $orders)->count(),
                    'learners' => (clone $learnersQuery)->count(),
                    'courses' => (clone $coursesQuery)->count(),
                    'enrollments' => (clone $enrollmentsQuery)->count(),
                ];
            })
            ->sortByDesc('revenue')
            ->values();
    }

    /**
     * @return array{institutes: Collection, users: Collection, learners: Collection, orders: Collection, revenue: Collection}
     */
    public function growthSeries(Carbon $from, Carbon $to): array
    {
        $learnerRoleId = Role::where('slug', 'learner')->value('id');

        return [
            'institutes' => $this->dailySeries(
                Company::query()
                    ->whereBetween('created_at', [$from, $to])
                    ->selectRaw('DATE(created_at) as day, COUNT(*) as value')
                    ->groupBy('day')
                    ->pluck('value', 'day'),
                $from,
                $to
            ),
            'users' => $this->dailySeries(
                User::query()
                    ->whereBetween('created_at', [$from, $to])
                    ->selectRaw('DATE(created_at) as day, COUNT(*) as value')
                    ->groupBy('day')
                    ->pluck('value', 'day'),
                $from,
                $to
            ),
            'learners' => $this->dailySeries(
                User::query()
                    ->when($learnerRoleId, fn ($q) => $q->where('role_id', $learnerRoleId))
                    ->whereBetween('created_at', [$from, $to])
                    ->selectRaw('DATE(created_at) as day, COUNT(*) as value')
                    ->groupBy('day')
                    ->pluck('value', 'day'),
                $from,
                $to
            ),
            'orders' => $this->dailySeries(
                Order::query()
                    ->whereBetween('created_at', [$from, $to])
                    ->selectRaw('DATE(created_at) as day, COUNT(*) as value')
                    ->groupBy('day')
                    ->pluck('value', 'day'),
                $from,
                $to
            ),
            'revenue' => $this->dailySeries(
                Order::query()
                    ->where('payment_status', 'paid')
                    ->whereBetween('created_at', [$from, $to])
                    ->selectRaw('DATE(created_at) as day, SUM(total) as value')
                    ->groupBy('day')
                    ->pluck('value', 'day'),
                $from,
                $to,
                asFloat: true
            ),
        ];
    }

    /**
     * Aggregate platform signup onboarding answers stored on users.notes.
     *
     * @return array{stats: array, steps: array, breakdowns: array<string, Collection>, rows: Collection}
     */
    public function signupFunnel(?Carbon $from = null, ?Carbon $to = null): array
    {
        $adminRoleId = Role::where('slug', 'admin')->value('id');
        $questionKeys = array_keys(SignupFormService::questions());

        $owners = User::query()
            ->when($adminRoleId, fn ($q) => $q->where('role_id', $adminRoleId))
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->get(['id', 'name', 'email', 'notes', 'created_at', 'is_active']);

        $companiesByOwner = Company::query()
            ->whereIn('owner_user_id', $owners->pluck('id'))
            ->get()
            ->keyBy('owner_user_id');

        $withOnboarding = 0;
        $withCompany = 0;
        $breakdowns = collect($questionKeys)->mapWithKeys(fn ($k) => [$k => collect()])->all();
        $stepCounts = collect($questionKeys)->mapWithKeys(fn ($k) => [$k => 0])->all();
        $rows = collect();

        foreach ($owners as $user) {
            $onboarding = $this->parseOnboarding($user->notes);
            $hasOnboarding = $onboarding !== [];
            if ($hasOnboarding) {
                $withOnboarding++;
            }

            $company = $companiesByOwner->get($user->id);
            if ($company) {
                $withCompany++;
            }

            foreach ($questionKeys as $key) {
                if (! empty($onboarding[$key])) {
                    $stepCounts[$key]++;
                    $value = (string) $onboarding[$key];
                    $breakdowns[$key][$value] = ($breakdowns[$key][$value] ?? 0) + 1;
                }
            }

            $rows->push([
                'user' => $user,
                'company' => $company,
                'onboarding' => $onboarding,
                'has_onboarding' => $hasOnboarding,
            ]);
        }

        foreach ($breakdowns as $key => $counts) {
            $breakdowns[$key] = collect($counts)->sortDesc();
        }

        $total = $owners->count();

        return [
            'stats' => [
                'owners' => $total,
                'with_onboarding' => $withOnboarding,
                'without_onboarding' => max(0, $total - $withOnboarding),
                'with_company' => $withCompany,
                'completion_rate' => $total > 0 ? round(($withOnboarding / $total) * 100, 1) : 0,
            ],
            'steps' => $stepCounts,
            'breakdowns' => $breakdowns,
            'rows' => $rows,
            'questions' => SignupFormService::questions(),
        ];
    }

    public function parseOnboarding(mixed $notes): array
    {
        if (blank($notes)) {
            return [];
        }

        $decoded = is_array($notes) ? $notes : json_decode((string) $notes, true);
        if (! is_array($decoded)) {
            return [];
        }

        $onboarding = $decoded['onboarding'] ?? $decoded;
        if (! is_array($onboarding)) {
            return [];
        }

        return collect($onboarding)
            ->only(array_keys(SignupFormService::questions()))
            ->filter(fn ($v) => filled($v))
            ->all();
    }

    public function optionLabel(string $question, string $value): string
    {
        $defaults = SignupFormService::defaults($question);
        foreach ($defaults['options'] ?? [] as $option) {
            if (($option['value'] ?? '') === $value) {
                return (string) ($option['label'] ?? $value);
            }
        }

        return $value;
    }

    /**
     * @param  Collection<string, mixed>  $raw
     * @return Collection<string, float|int>
     */
    public function dailySeries(Collection $raw, Carbon $from, Carbon $to, bool $asFloat = false): Collection
    {
        $normalized = $raw->mapWithKeys(function ($value, $key) {
            $day = Carbon::parse((string) $key)->toDateString();

            return [$day => $value];
        });

        $series = collect();
        $cursor = $from->copy()->startOfDay();
        $end = $to->copy()->startOfDay();

        while ($cursor->lte($end)) {
            $key = $cursor->toDateString();
            $value = $normalized->get($key, 0);
            $series->put($key, $asFloat ? (float) $value : (int) $value);
            $cursor->addDay();
        }

        return $series;
    }
}
