<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Resource extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'resource_type',
        'file_path', 'external_url', 'category_id', 'status', 'published_at', 'download_count',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Resource $resource) {
            if (empty($resource->slug)) {
                $resource->slug = Str::slug($resource->title);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function downloads()
    {
        return $this->hasMany(ResourceDownload::class);
    }
}
