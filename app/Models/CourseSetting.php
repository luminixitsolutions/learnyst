<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseSetting extends Model
{
    protected $fillable = [
        'course_id', 'certificate_enabled', 'discussion_enabled', 'reviews_enabled',
        'max_video_upload_mb', 'max_audio_upload_mb', 'max_pdf_upload_mb', 'extra',
    ];

    protected function casts(): array
    {
        return [
            'certificate_enabled' => 'boolean',
            'discussion_enabled' => 'boolean',
            'reviews_enabled' => 'boolean',
            'max_video_upload_mb' => 'integer',
            'max_audio_upload_mb' => 'integer',
            'max_pdf_upload_mb' => 'integer',
            'extra' => 'array',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
