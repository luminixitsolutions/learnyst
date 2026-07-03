<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduledEvent extends Model
{
    protected $fillable = [
        'title', 'description', 'batch_id', 'course_id', 'instructor_id', 'created_by',
        'starts_at', 'ends_at', 'meeting_url', 'platform', 'status', 'type',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
}
