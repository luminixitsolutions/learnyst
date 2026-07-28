<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Coupon extends Model
{
    protected $fillable = [
        'created_by',
        'code',
        'title',
        'description',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'max_uses',
        'per_user_limit',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'starts_at' => 'date',
            'expires_at' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'coupon_course');
    }

    public function isValidForUse(?int $userId = null, ?int $courseId = null): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $today = Carbon::today();
        if ($this->starts_at && $today->lt($this->starts_at)) {
            return false;
        }
        if ($this->expires_at && $today->gt($this->expires_at)) {
            return false;
        }
        if ($this->max_uses !== null && (int) $this->used_count >= (int) $this->max_uses) {
            return false;
        }
        if ($courseId && $this->courses()->exists() && ! $this->courses()->where('courses.id', $courseId)->exists()) {
            return false;
        }
        if ($userId && $this->per_user_limit) {
            $userUses = $this->orders()->where('user_id', $userId)->where('payment_status', 'paid')->count();
            if ($userUses >= (int) $this->per_user_limit) {
                return false;
            }
        }

        return true;
    }
}
