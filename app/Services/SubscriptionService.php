<?php

namespace App\Services;

use App\Models\LearnerSubscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubscriptionService
{
    public function createSubscription(
        SubscriptionPlan $plan,
        User $learner,
        ?int $createdBy = null,
        array $attributes = []
    ): LearnerSubscription {
        if (! $plan->is_active) {
            throw ValidationException::withMessages(['plan' => 'This subscription plan is inactive.']);
        }

        $createdBy = $createdBy ?? $plan->created_by;
        $now = now();
        $trialDays = (int) ($attributes['trial_days'] ?? $plan->trial_days);
        $billingDays = $plan->resolveBillingDays();
        $autoRenew = array_key_exists('auto_renew', $attributes)
            ? (bool) $attributes['auto_renew']
            : (bool) $plan->auto_renew;

        $startsAt = isset($attributes['starts_at'])
            ? Carbon::parse($attributes['starts_at'])
            : $now->copy();

        $trialEndsAt = $trialDays > 0 ? $startsAt->copy()->addDays($trialDays) : null;
        $status = $trialDays > 0 ? 'trialing' : 'active';
        $periodStart = $trialEndsAt?->copy() ?? $startsAt->copy();
        $endsAt = $periodStart->copy()->addDays($billingDays);
        $nextBillingAt = $autoRenew ? $endsAt->copy() : null;

        $subscription = LearnerSubscription::create([
            'created_by' => $createdBy,
            'user_id' => $learner->id,
            'subscription_plan_id' => $plan->id,
            'order_id' => $attributes['order_id'] ?? null,
            'course_pricing_plan_id' => $attributes['course_pricing_plan_id'] ?? null,
            'status' => $status,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'trial_ends_at' => $trialEndsAt,
            'next_billing_at' => $nextBillingAt,
            'auto_renew' => $autoRenew,
            'amount' => $attributes['amount'] ?? $plan->price,
            'notes' => $attributes['notes'] ?? null,
            'meta' => $attributes['meta'] ?? null,
        ]);

        ActivityLogger::log(
            'subscription_created',
            "Subscription #{$subscription->id} assigned to {$learner->name} ({$plan->title})",
            $subscription
        );

        return $subscription;
    }

    public function renew(LearnerSubscription $subscription): LearnerSubscription
    {
        if (! in_array($subscription->status, ['active', 'trialing', 'expired'], true)) {
            throw ValidationException::withMessages(['subscription' => 'Subscription cannot be renewed in its current status.']);
        }

        $plan = $subscription->plan;
        $billingDays = $plan?->resolveBillingDays() ?? 30;
        $from = $subscription->ends_at && $subscription->ends_at->isFuture()
            ? $subscription->ends_at->copy()
            : now();

        $endsAt = $from->copy()->addDays($billingDays);

        $subscription->update([
            'status' => 'active',
            'ends_at' => $endsAt,
            'next_billing_at' => $subscription->auto_renew ? $endsAt->copy() : null,
            'paused_at' => null,
            'cancelled_at' => null,
        ]);

        ActivityLogger::log(
            'subscription_renewed',
            "Subscription #{$subscription->id} renewed until {$endsAt->toDateString()}",
            $subscription
        );

        return $subscription->fresh();
    }

    public function cancel(LearnerSubscription $subscription, ?string $notes = null): LearnerSubscription
    {
        if (! $subscription->isCancellable()) {
            throw ValidationException::withMessages(['subscription' => 'Subscription cannot be cancelled.']);
        }

        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'auto_renew' => false,
            'next_billing_at' => null,
            'notes' => $notes ?? $subscription->notes,
        ]);

        ActivityLogger::log(
            'subscription_cancelled',
            "Subscription #{$subscription->id} cancelled",
            $subscription
        );

        return $subscription->fresh();
    }

    public function pause(LearnerSubscription $subscription, ?string $notes = null): LearnerSubscription
    {
        if (! $subscription->isPausable()) {
            throw ValidationException::withMessages(['subscription' => 'Only active or trialing subscriptions can be paused.']);
        }

        $subscription->update([
            'status' => 'paused',
            'paused_at' => now(),
            'next_billing_at' => null,
            'notes' => $notes ?? $subscription->notes,
        ]);

        ActivityLogger::log(
            'subscription_paused',
            "Subscription #{$subscription->id} paused",
            $subscription
        );

        return $subscription->fresh();
    }

    public function resume(LearnerSubscription $subscription): LearnerSubscription
    {
        if (! $subscription->isResumable()) {
            throw ValidationException::withMessages(['subscription' => 'Only paused subscriptions can be resumed.']);
        }

        $endsAt = $subscription->ends_at && $subscription->ends_at->isFuture()
            ? $subscription->ends_at
            : now()->addDays($subscription->plan?->resolveBillingDays() ?? 30);

        $subscription->update([
            'status' => 'active',
            'paused_at' => null,
            'ends_at' => $endsAt,
            'next_billing_at' => $subscription->auto_renew ? $endsAt->copy() : null,
        ]);

        ActivityLogger::log(
            'subscription_resumed',
            "Subscription #{$subscription->id} resumed",
            $subscription
        );

        return $subscription->fresh();
    }

    /** Mark due subscriptions as expired. Returns count updated. */
    public function expireDue(?int $createdBy = null): int
    {
        return DB::transaction(function () use ($createdBy) {
            $query = LearnerSubscription::query()
                ->whereIn('status', ['active', 'trialing', 'paused'])
                ->whereNotNull('ends_at')
                ->where('ends_at', '<', now());

            if ($createdBy) {
                $query->where('created_by', $createdBy);
            }

            $ids = $query->pluck('id');
            if ($ids->isEmpty()) {
                return 0;
            }

            $count = LearnerSubscription::whereIn('id', $ids)->update([
                'status' => 'expired',
                'next_billing_at' => null,
                'updated_at' => now(),
            ]);

            ActivityLogger::log(
                'subscriptions_expired',
                "Expired {$count} due subscription(s)",
                null,
                ['ids' => $ids->all()]
            );

            return $count;
        });
    }
}
