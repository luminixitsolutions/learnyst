<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'created_by', 'title', 'slug', 'description', 'plan_type',
        'product_type', 'product_id', 'billing_cycle', 'billing_days',
        'price', 'setup_fee', 'currency', 'trial_days', 'auto_renew',
        'is_active', 'meta',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'setup_fee' => 'decimal:2',
            'billing_days' => 'integer',
            'trial_days' => 'integer',
            'product_id' => 'integer',
            'auto_renew' => 'boolean',
            'is_active' => 'boolean',
            'meta' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (SubscriptionPlan $plan) {
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->title);
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function subscriptions()
    {
        return $this->hasMany(LearnerSubscription::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function billingCycleLabel(): string
    {
        return match ($this->billing_cycle) {
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'yearly' => 'Yearly',
            'custom' => 'Custom ('.$this->billing_days.' days)',
            default => ucfirst((string) $this->billing_cycle),
        };
    }

    public function resolveBillingDays(): int
    {
        return match ($this->billing_cycle) {
            'monthly' => 30,
            'quarterly' => 90,
            'yearly' => 365,
            default => max(1, (int) $this->billing_days),
        };
    }
}
