<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ReferralCode extends Model
{
    protected $fillable = [
        'user_id',
        'created_by',
        'code',
        'uses_count',
        'max_uses',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'uses_count' => 'integer',
            'max_uses' => 'integer',
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

    public function referrals()
    {
        return $this->hasMany(Referral::class);
    }

    public function canBeUsed(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->max_uses !== null && $this->uses_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    public static function generateUniqueCode(User $user): string
    {
        $base = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', Str::limit($user->name, 6, '')) ?: 'REF');
        do {
            $code = $base.strtoupper(Str::random(4));
        } while (static::where('code', $code)->exists());

        return $code;
    }
}
