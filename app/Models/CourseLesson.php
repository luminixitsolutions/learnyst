<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLesson extends Model
{
    public const TYPES = [
        'video' => 'Video',
        'audio' => 'Audio',
        'pdf' => 'PDF',
        'text' => 'Text',
        'quiz' => 'Quiz',
        'assignment' => 'Assignment',
        'live_class' => 'Live Class',
        'code' => 'Code / Coding Lesson',
        'external_link' => 'External Link / Embed',
    ];

    protected $fillable = [
        'course_section_id', 'title', 'lesson_type', 'status', 'content',
        'video_url', 'external_url', 'file_path', 'media_processing_status',
        'duration_minutes', 'is_preview', 'is_locked', 'sort_order', 'drip_date',
        'drip_enabled', 'completion_required', 'allow_download', 'quiz_data', 'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_preview' => 'boolean',
            'is_locked' => 'boolean',
            'drip_enabled' => 'boolean',
            'completion_required' => 'boolean',
            'allow_download' => 'boolean',
            'drip_date' => 'date',
            'quiz_data' => 'array',
            'settings' => 'array',
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

    public function media()
    {
        return $this->hasMany(LessonMedia::class);
    }

    public function primaryMedia()
    {
        return $this->hasOne(LessonMedia::class)->latestOfMany();
    }

    public function attachments()
    {
        return $this->hasMany(LessonAttachment::class)->orderBy('sort_order');
    }

    public function liveClass()
    {
        return $this->hasOne(LiveClass::class);
    }

    public function typeLabel(): string
    {
        if ($this->lesson_type === 'pdf' && ($this->settings['sub_type'] ?? null) === 'slides') {
            return 'Slides';
        }

        return self::TYPES[$this->lesson_type] ?? ucfirst(str_replace('_', ' ', $this->lesson_type));
    }

    public function editorRoute(): string
    {
        return route('admin.lessons.edit', $this);
    }
}
