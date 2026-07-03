<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckoutConsent extends Model
{
    protected $fillable = [
        'title', 'description', 'body', 'is_required', 'is_active', 'show_on_checkout', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'is_active' => 'boolean',
            'show_on_checkout' => 'boolean',
        ];
    }

    public function orderConsents()
    {
        return $this->hasMany(OrderConsent::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
