<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = [
        'referral_code_id',
        'referrer_id',
        'referred_id',
        'created_by',
        'status',
        'reward_type',
        'referrer_reward',
        'referred_reward',
        'qualified_at',
        'rewarded_at',
        'order_id',
        'notes',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'referrer_reward' => 'decimal:2',
            'referred_reward' => 'decimal:2',
            'qualified_at' => 'datetime',
            'rewarded_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function referralCode()
    {
        return $this->belongsTo(ReferralCode::class);
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referred()
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
