<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Badge extends Model
{
    protected $fillable = [
        'created_by', 'name', 'slug', 'description', 'icon',
        'criteria_type', 'criteria_value', 'xp_reward', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::creating(function (Badge $badge) {
            if (empty($badge->slug)) {
                $badge->slug = Str::slug($badge->name);
            }
        });
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'badge_user')
            ->withPivot('awarded_at')
            ->withTimestamps();
    }
}
