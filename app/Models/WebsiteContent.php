<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class WebsiteContent extends Model
{
    protected $fillable = [
        'key',
        'label',
        'group',
        'content',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'sort_order' => 'integer',
        ];
    }

    public static function getContent(string $key, mixed $default = []): mixed
    {
        $row = Cache::remember("website_content.{$key}", 3600, function () use ($key) {
            return static::query()->where('key', $key)->first();
        });

        if (! $row || $row->content === null) {
            return $default;
        }

        return $row->content;
    }

    public static function putContent(string $key, string $label, array $content, string $group = 'home', int $sortOrder = 0): self
    {
        $row = static::updateOrCreate(
            ['key' => $key],
            [
                'label' => $label,
                'group' => $group,
                'content' => $content,
                'sort_order' => $sortOrder,
            ]
        );

        Cache::forget("website_content.{$key}");

        return $row;
    }

    public static function mediaUrl(?string $path): string
    {
        if (! $path) {
            return '';
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) {
            return $path;
        }

        if (str_starts_with($path, 'website/')) {
            return asset($path);
        }

        return asset('storage/'.ltrim($path, '/'));
    }
}
