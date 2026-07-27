<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'created_by',
        'name',
        'email',
        'phone',
        'organization',
        'contact_type',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function typeLabel(): string
    {
        return match ($this->contact_type) {
            'customer' => 'Customer',
            'partner' => 'Partner',
            'vendor' => 'Vendor',
            default => 'Lead',
        };
    }
}
