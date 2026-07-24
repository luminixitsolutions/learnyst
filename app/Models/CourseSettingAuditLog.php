<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseSettingAuditLog extends Model
{
    protected $fillable = [
        'course_id', 'section', 'action', 'user_id', 'before', 'after', 'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'before' => 'array',
            'after' => 'array',
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
}
