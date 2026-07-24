<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CompanyBlog extends Model
{
    protected $fillable = [
        'company_id', 'title', 'slug', 'excerpt', 'body', 'cover_image', 'is_published', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function coverUrl(): string
    {
        return Company::mediaUrl($this->cover_image);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public static function uniqueSlugForCompany(int $companyId, string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'post';
        $slug = $base;
        $i = 1;

        while (static::query()
            ->where('company_id', $companyId)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }
}
