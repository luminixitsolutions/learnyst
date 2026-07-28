<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliatePayout extends Model
{
    protected $fillable = [
        'affiliate_id',
        'created_by',
        'amount',
        'status',
        'payment_reference',
        'notes',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function statusBadgeType(): string
    {
        return match ($this->status) {
            'paid', 'approved' => 'success',
            'pending' => 'warning',
            'rejected' => 'danger',
            default => 'default',
        };
    }
}
