<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LessonAttachment extends Model
{
    protected $fillable = [
        'course_lesson_id', 'title', 'file_path', 'file_type',
        'download_allowed', 'status', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'download_allowed' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function lesson()
    {
        return $this->belongsTo(CourseLesson::class, 'course_lesson_id');
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }
}
