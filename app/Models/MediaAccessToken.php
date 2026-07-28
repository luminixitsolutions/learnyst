<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaAccessToken extends Model
{
    protected $fillable = [
        'user_id', 'course_lesson_id', 'token', 'expires_at',
        'max_seconds', 'watched_seconds', 'device_id', 'revoked_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(CourseLesson::class, 'course_lesson_id');
    }

    public function isValid(): bool
    {
        return $this->revoked_at === null && $this->expires_at->isFuture();
    }
}
