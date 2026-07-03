<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLesson extends Model
{
    protected $fillable = [
        'course_section_id', 'title', 'lesson_type', 'content',
        'video_url', 'file_path', 'duration_minutes', 'is_preview', 'is_locked',
        'sort_order', 'drip_date', 'quiz_data',
    ];

    protected function casts(): array
    {
        return [
            'is_preview' => 'boolean',
            'is_locked' => 'boolean',
            'drip_date' => 'date',
            'quiz_data' => 'array',
        ];
    }

    public function section()
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }

    public function course()
    {
        return $this->hasOneThrough(Course::class, CourseSection::class, 'id', 'id', 'course_section_id', 'course_id');
    }
}
