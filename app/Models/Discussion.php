<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discussion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'body', 'user_id', 'course_id', 'batch_id',
        'community_id', 'is_locked', 'is_reported', 'is_resolved', 'resolved_at', 'resolved_by', 'replies_count',
    ];

    protected function casts(): array
    {
        return [
            'is_locked' => 'boolean',
            'is_reported' => 'boolean',
            'is_resolved' => 'boolean',
            'resolved_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function comments()
    {
        return $this->hasMany(DiscussionComment::class);
    }
}
