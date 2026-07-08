<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LessonMedia extends Model
{
    protected $fillable = [
        'course_lesson_id', 'media_type', 'file_path', 'file_url',
        'mime_type', 'file_size', 'duration_seconds', 'processing_status',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'duration_seconds' => 'integer',
        ];
    }

    public function lesson()
    {
        return $this->belongsTo(CourseLesson::class, 'course_lesson_id');
    }

    public function getUrlAttribute(): ?string
    {
        if ($this->file_url) {
            return $this->file_url;
        }

        return $this->file_path ? Storage::url($this->file_path) : null;
    }
}
