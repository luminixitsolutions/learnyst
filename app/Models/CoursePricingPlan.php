<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoursePricingPlan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id', 'title', 'slug', 'plan_type', 'status', 'currency',
        'regular_price', 'offer_price', 'tax_inclusive', 'description',
        'is_public', 'lifetime_access', 'validity_days', 'access_expires_at',
        'purchase_starts_at', 'purchase_ends_at', 'offer_starts_at', 'offer_ends_at',
        'show_countdown', 'enrollment_limit', 'enrollment_count', 'sales_count',
        'coupon_eligible', 'billing_frequency', 'trial_days', 'setup_fee',
        'billing_cycles', 'auto_renew', 'installment_config', 'refund_config',
        'meta', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'regular_price' => 'decimal:2',
            'offer_price' => 'decimal:2',
            'setup_fee' => 'decimal:2',
            'tax_inclusive' => 'boolean',
            'is_public' => 'boolean',
            'lifetime_access' => 'boolean',
            'show_countdown' => 'boolean',
            'coupon_eligible' => 'boolean',
            'auto_renew' => 'boolean',
            'validity_days' => 'integer',
            'enrollment_limit' => 'integer',
            'enrollment_count' => 'integer',
            'sales_count' => 'integer',
            'trial_days' => 'integer',
            'billing_cycles' => 'integer',
            'access_expires_at' => 'datetime',
            'purchase_starts_at' => 'datetime',
            'purchase_ends_at' => 'datetime',
            'offer_starts_at' => 'datetime',
            'offer_ends_at' => 'datetime',
            'installment_config' => 'array',
            'refund_config' => 'array',
            'meta' => 'array',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeOfStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
