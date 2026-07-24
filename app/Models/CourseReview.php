<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseReview extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id', 'user_id', 'reviewer_name', 'reviewer_email', 'rating', 'review', 'is_anonymous',
        'status', 'moderated_by', 'moderated_at', 'moderation_note',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_anonymous' => 'boolean',
            'moderated_at' => 'datetime',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function moderatedBy()
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function displayName(): string
    {
        if ($this->is_anonymous) {
            return 'Anonymous learner';
        }

        return $this->reviewer_name
            ?: ($this->user?->name ?? 'Learner');
    }
}
