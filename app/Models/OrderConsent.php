<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderConsent extends Model
{
    protected $fillable = [
        'order_id', 'checkout_consent_id', 'user_id',
        'accepted', 'accepted_at', 'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'accepted' => 'boolean',
            'accepted_at' => 'datetime',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function consent()
    {
        return $this->belongsTo(CheckoutConsent::class, 'checkout_consent_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
