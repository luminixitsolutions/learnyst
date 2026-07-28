<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Affiliate extends Model
{
    protected $fillable = [
        'user_id',
        'created_by',
        'name',
        'email',
        'phone',
        'code',
        'status',
        'commission_type',
        'commission_value',
        'total_clicks',
        'total_sales',
        'total_commission',
        'paid_commission',
        'payment_details',
        'notes',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'commission_value' => 'decimal:2',
            'total_clicks' => 'integer',
            'total_sales' => 'decimal:2',
            'total_commission' => 'decimal:2',
            'paid_commission' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function links()
    {
        return $this->hasMany(AffiliateLink::class);
    }

    public function commissions()
    {
        return $this->hasMany(AffiliateCommission::class);
    }

    public function payouts()
    {
        return $this->hasMany(AffiliatePayout::class);
    }

    public function pendingCommissionBalance(): float
    {
        return max(0, (float) $this->total_commission - (float) $this->paid_commission);
    }

    public function statusBadgeType(): string
    {
        return match ($this->status) {
            'approved' => 'success',
            'pending' => 'warning',
            'suspended' => 'danger',
            'rejected' => 'danger',
            default => 'default',
        };
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public static function generateUniqueCode(?string $name = null): string
    {
        $base = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', Str::limit($name ?? 'AFF', 6, '')) ?: 'AFF');
        do {
            $code = $base.strtoupper(Str::random(4));
        } while (static::where('code', $code)->exists());

        return $code;
    }
}
