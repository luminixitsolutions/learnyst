<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearnerSubscription extends Model
{
    protected $fillable = [
        'created_by', 'user_id', 'subscription_plan_id', 'order_id',
        'course_pricing_plan_id', 'status', 'starts_at', 'ends_at',
        'trial_ends_at', 'next_billing_at', 'cancelled_at', 'paused_at',
        'auto_renew', 'amount', 'notes', 'meta',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'trial_ends_at' => 'datetime',
            'next_billing_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'paused_at' => 'datetime',
            'auto_renew' => 'boolean',
            'amount' => 'decimal:2',
            'meta' => 'array',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function coursePricingPlan()
    {
        return $this->belongsTo(CoursePricingPlan::class, 'course_pricing_plan_id');
    }

    public function statusLabel(): string
    {
        return ucfirst((string) $this->status);
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'trialing', 'active', 'paused'], true);
    }

    public function isPausable(): bool
    {
        return in_array($this->status, ['active', 'trialing'], true);
    }

    public function isResumable(): bool
    {
        return $this->status === 'paused';
    }
}
