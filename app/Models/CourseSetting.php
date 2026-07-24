<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseSetting extends Model
{
    protected $fillable = [
        'course_id', 'certificate_enabled', 'discussion_enabled', 'reviews_enabled',
        'bookmarks_enabled', 'leaderboard_enabled', 'drip_mode', 'learning_path_enabled',
        'sell_independently', 'access_visibility', 'selling_platforms', 'permissions',
        'review_config', 'discussion_config', 'bookmark_config', 'leaderboard_config',
        'certificate_config', 'drip_config', 'learner_config', 'learning_path_config',
        'android_pricing', 'ios_pricing', 'branding', 'seo',
        'max_video_upload_mb', 'max_audio_upload_mb', 'max_pdf_upload_mb', 'extra',
        'trash_retention_days', 'deleted_by', 'deletion_reason',
        'published_at', 'published_by',
    ];

    protected function casts(): array
    {
        return [
            'certificate_enabled' => 'boolean',
            'discussion_enabled' => 'boolean',
            'reviews_enabled' => 'boolean',
            'bookmarks_enabled' => 'boolean',
            'leaderboard_enabled' => 'boolean',
            'learning_path_enabled' => 'boolean',
            'sell_independently' => 'boolean',
            'max_video_upload_mb' => 'integer',
            'max_audio_upload_mb' => 'integer',
            'max_pdf_upload_mb' => 'integer',
            'trash_retention_days' => 'integer',
            'selling_platforms' => 'array',
            'permissions' => 'array',
            'review_config' => 'array',
            'discussion_config' => 'array',
            'bookmark_config' => 'array',
            'leaderboard_config' => 'array',
            'certificate_config' => 'array',
            'drip_config' => 'array',
            'learner_config' => 'array',
            'learning_path_config' => 'array',
            'android_pricing' => 'array',
            'ios_pricing' => 'array',
            'branding' => 'array',
            'seo' => 'array',
            'extra' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function publishedBy()
    {
        return $this->belongsTo(User::class, 'published_by');
    }
}
