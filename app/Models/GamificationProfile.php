<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GamificationProfile extends Model
{
    protected $fillable = [
        'user_id', 'created_by', 'xp', 'level',
        'current_streak', 'longest_streak', 'last_activity_date',
    ];

    protected function casts(): array
    {
        return ['last_activity_date' => 'date'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(XpTransaction::class);
    }

    public static function levelForXp(int $xp): int
    {
        return max(1, (int) floor($xp / 100) + 1);
    }

    public function xpToNextLevel(): int
    {
        return ($this->level * 100) - $this->xp;
    }
}
