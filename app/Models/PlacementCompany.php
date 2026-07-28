<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PlacementCompany extends Model
{
    protected $fillable = [
        'created_by', 'name', 'slug', 'industry', 'website',
        'contact_name', 'contact_email', 'contact_phone', 'about', 'logo', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::creating(function (self $c) {
            if (empty($c->slug)) {
                $c->slug = Str::slug($c->name).'-'.Str::lower(Str::random(4));
            }
        });
    }

    public function jobs()
    {
        return $this->hasMany(PlacementJob::class);
    }
}
