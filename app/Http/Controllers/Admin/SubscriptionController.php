<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\LearnerSubscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SubscriptionController extends Controller
{
    use ScopesToCurrentUser;

    public function __construct(protected SubscriptionService $subscriptions) {}

    public function plans(Request $request)
    {
        $this->subscriptions->expireDue($this->currentUserId());

        $query = $this->owned(SubscriptionPlan::query())
            ->withCount('subscriptions')
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('plan_type')) {
            $query->where('plan_type', $request->plan_type);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $plans = $query->paginate(20)->withQueryString();

        $stats = [
            'plans' => (clone $this->owned(SubscriptionPlan::query()))->count(),
            'active_plans' => (clone $this->owned(SubscriptionPlan::query()))->where('is_active', true)->count(),
            'subscriptions' => (clone $this->owned(LearnerSubscription::query()))->count(),
            'active_subs' => (clone $this->owned(LearnerSubscription::query()))->whereIn('status', ['active', 'trialing'])->count(),
        ];

        return view('admin.subscriptions.plans', compact('plans', 'stats'));
    }

    public function create()
    {
        return view('admin.subscriptions.plan-form', [
            'plan' => new SubscriptionPlan([
                'plan_type' => 'course',
                'billing_cycle' => 'monthly',
                'billing_days' => 30,
                'currency' => 'INR',
                'auto_renew' => true,
                'is_active' => true,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatePlan($request);
        $validated['created_by'] = Auth::id();
        $validated['slug'] = $this->uniqueSlug($validated['slug'] ?? $validated['title']);
        $validated['billing_days'] = $this->billingDaysFor($validated['billing_cycle'], $validated['billing_days'] ?? null);
        $validated['auto_renew'] = $request->boolean('auto_renew');
        $validated['is_active'] = $request->boolean('is_active', true);

        $plan = SubscriptionPlan::create($validated);

        ActivityLogger::log('subscription_plan_created', "Plan \"{$plan->title}\" created", $plan);

        return redirect()
            ->route('admin.subscriptions.plans')
            ->with('success', 'Subscription plan created.');
    }

    public function edit(SubscriptionPlan $plan)
    {
        $this->authorizeOwner($plan);

        return view('admin.subscriptions.plan-form', compact('plan'));
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $this->authorizeOwner($plan);

        $validated = $this->validatePlan($request, $plan);
        $validated['slug'] = $this->uniqueSlug($validated['slug'] ?? $validated['title'], $plan->id);
        $validated['billing_days'] = $this->billingDaysFor($validated['billing_cycle'], $validated['billing_days'] ?? null);
        $validated['auto_renew'] = $request->boolean('auto_renew');
        $validated['is_active'] = $request->boolean('is_active');

        $plan->update($validated);

        ActivityLogger::log('subscription_plan_updated', "Plan \"{$plan->title}\" updated", $plan);

        return redirect()
            ->route('admin.subscriptions.plans')
            ->with('success', 'Subscription plan updated.');
    }

    public function index(Request $request)
    {
        $this->subscriptions->expireDue($this->currentUserId());

        $query = $this->owned(LearnerSubscription::query())
            ->with(['user', 'plan'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('plan', fn ($p) => $p->where('title', 'like', "%{$search}%"));
            });
        }

        $subscriptions = $query->paginate(20)->withQueryString();
        $plans = $this->owned(SubscriptionPlan::query())->active()->orderBy('title')->get();
        $learners = $this->visibleLearnersQuery()->orderBy('name')->get();

        return view('admin.subscriptions.index', compact('subscriptions', 'plans', 'learners'));
    }

    public function show(LearnerSubscription $subscription)
    {
        $this->authorizeOwner($subscription);
        $subscription->load(['user', 'plan', 'order', 'coursePricingPlan']);

        return view('admin.subscriptions.show', compact('subscription'));
    }

    public function storeSubscription(Request $request)
    {
        $ownedLearnerIds = $this->visibleLearnersQuery()->pluck('id');
        $ownedPlanIds = $this->owned(SubscriptionPlan::query())->pluck('id');

        $validated = $request->validate([
            'user_id' => ['required', Rule::in($ownedLearnerIds)],
            'subscription_plan_id' => ['required', Rule::in($ownedPlanIds)],
            'auto_renew' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'course_pricing_plan_id' => ['nullable', 'integer'],
        ]);

        $plan = SubscriptionPlan::findOrFail($validated['subscription_plan_id']);
        $learner = User::findOrFail($validated['user_id']);

        $this->subscriptions->createSubscription($plan, $learner, Auth::id(), [
            'auto_renew' => $request->boolean('auto_renew', $plan->auto_renew),
            'notes' => $validated['notes'] ?? null,
            'course_pricing_plan_id' => $validated['course_pricing_plan_id'] ?? null,
        ]);

        return redirect()
            ->route('admin.subscriptions.index')
            ->with('success', 'Subscription assigned to learner.');
    }

    public function cancel(Request $request, LearnerSubscription $subscription)
    {
        $this->authorizeOwner($subscription);

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->subscriptions->cancel($subscription, $validated['notes'] ?? null);

        return back()->with('success', 'Subscription cancelled.');
    }

    public function pause(Request $request, LearnerSubscription $subscription)
    {
        $this->authorizeOwner($subscription);

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->subscriptions->pause($subscription, $validated['notes'] ?? null);

        return back()->with('success', 'Subscription paused.');
    }

    public function resume(LearnerSubscription $subscription)
    {
        $this->authorizeOwner($subscription);
        $this->subscriptions->resume($subscription);

        return back()->with('success', 'Subscription resumed.');
    }

    protected function validatePlan(Request $request, ?SubscriptionPlan $plan = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'plan_type' => ['required', Rule::in(['course', 'bundle', 'test_series', 'platform'])],
            'product_type' => ['nullable', 'string', 'max:100'],
            'product_id' => ['nullable', 'integer'],
            'billing_cycle' => ['required', Rule::in(['monthly', 'quarterly', 'yearly', 'custom'])],
            'billing_days' => ['nullable', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'setup_fee' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'trial_days' => ['nullable', 'integer', 'min:0'],
            'auto_renew' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    protected function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: 'plan';
        $slug = $base;
        $i = 1;

        while (
            SubscriptionPlan::query()
                ->where('created_by', Auth::id())
                ->where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    protected function billingDaysFor(string $cycle, mixed $customDays): int
    {
        return match ($cycle) {
            'monthly' => 30,
            'quarterly' => 90,
            'yearly' => 365,
            default => max(1, (int) ($customDays ?: 30)),
        };
    }
}
