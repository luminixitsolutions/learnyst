<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class XpTransaction extends Model
{
    protected $fillable = [
        'gamification_profile_id', 'user_id', 'created_by', 'action_key',
        'points', 'source_type', 'source_id', 'course_id', 'meta',
    ];

    protected function casts(): array
    {
        return ['meta' => 'array'];
    }

    public function profile()
    {
        return $this->belongsTo(GamificationProfile::class, 'gamification_profile_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function source()
    {
        return $this->morphTo();
    }
}
