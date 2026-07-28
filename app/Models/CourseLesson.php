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

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class, 'course_lesson_id');
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

    public function hasPlayableMedia(): bool
    {
        return filled($this->video_url) || filled($this->file_path) || filled($this->external_url);
    }

    public function fileUrl(): ?string
    {
        if (! $this->file_path) {
            return null;
        }

        $path = ltrim($this->file_path, '/');

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, 'website/')) {
            return '/'.$path;
        }

        if (str_starts_with($path, 'storage/')) {
            return '/'.$path;
        }

        return '/storage/'.$path;
    }

    /**
     * Normalize YouTube/Vimeo/watch URLs into an embeddable iframe src.
     */
    public function embedSrc(): ?string
    {
        $url = trim((string) ($this->video_url ?: $this->external_url ?: ''));
        if ($url === '') {
            return null;
        }

        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([A-Za-z0-9_-]+)/', $url, $m)) {
            return 'https://www.youtube.com/embed/'.$m[1];
        }

        if (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $url, $m)) {
            return 'https://player.vimeo.com/video/'.$m[1];
        }

        // Already an embed URL or direct media URL
        return $url;
    }

    public function isExternalEmbed(): bool
    {
        $url = (string) ($this->video_url ?: $this->external_url ?: '');

        return (bool) preg_match('/youtube\.com|youtu\.be|vimeo\.com/i', $url);
    }
}
