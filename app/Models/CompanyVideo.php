<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyVideo extends Model
{
    protected $fillable = [
        'company_id', 'title', 'description', 'video_url', 'thumbnail', 'is_published', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function thumbnailUrl(): string
    {
        return Company::mediaUrl($this->thumbnail);
    }

    public function embedUrl(): string
    {
        $url = trim((string) $this->video_url);
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([A-Za-z0-9_-]+)/', $url, $m)) {
            return 'https://www.youtube.com/embed/'.$m[1];
        }
        if (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $url, $m)) {
            return 'https://player.vimeo.com/video/'.$m[1];
        }

        return $url;
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
