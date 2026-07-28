<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    public const SOURCE_MANUAL = 'manual';
    public const SOURCE_TOPUP = 'topup';
    public const SOURCE_ADJUSTMENT = 'adjustment';
    public const SOURCE_ORDER_PAYMENT = 'order_payment';
    public const SOURCE_REFUND = 'refund';
    public const SOURCE_REFERRAL_BONUS = 'referral_bonus';
    public const SOURCE_SIGNUP_REWARD = 'signup_reward';
    public const SOURCE_AFFILIATE = 'affiliate';

    protected $fillable = [
        'wallet_id',
        'user_id',
        'created_by',
        'performed_by',
        'type',
        'source',
        'amount',
        'balance_after',
        'status',
        'reference_type',
        'reference_id',
        'referral_code',
        'notes',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'balance_after' => 'decimal:2',
            'meta' => 'array',
        ];
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function reference()
    {
        return $this->morphTo();
    }

    public function sourceLabel(): string
    {
        return match ($this->source) {
            self::SOURCE_TOPUP => 'Top-up',
            self::SOURCE_ADJUSTMENT => 'Adjustment',
            self::SOURCE_ORDER_PAYMENT => 'Course Purchase',
            self::SOURCE_REFUND => 'Refund',
            self::SOURCE_REFERRAL_BONUS => 'Referral Bonus',
            self::SOURCE_SIGNUP_REWARD => 'Signup Reward',
            self::SOURCE_AFFILIATE => 'Affiliate Commission',
            default => 'Manual',
        };
    }

    public function isCredit(): bool
    {
        return $this->type === 'credit';
    }
}
