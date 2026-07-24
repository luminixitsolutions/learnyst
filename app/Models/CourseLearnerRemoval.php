<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLearnerRemoval extends Model
{
    protected $fillable = [
        'course_id', 'user_id', 'enrollment_id', 'removed_by', 'reason',
        'snapshot', 'restored_at', 'restored_by',
    ];

    protected function casts(): array
    {
        return [
            'snapshot' => 'array',
            'restored_at' => 'datetime',
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

    public function enrollment()
    {
        return $this->belongsTo(CourseEnrollment::class, 'enrollment_id');
    }

    public function removedBy()
    {
        return $this->belongsTo(User::class, 'removed_by');
    }

    public function restoredBy()
    {
        return $this->belongsTo(User::class, 'restored_by');
    }
}
