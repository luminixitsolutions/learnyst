<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoursePublicationHistory extends Model
{
    protected $fillable = [
        'course_id', 'from_status', 'to_status', 'changed_by', 'notes', 'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
