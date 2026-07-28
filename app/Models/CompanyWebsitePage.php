<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CompanyWebsitePage extends Model
{
    protected $fillable = [
        'company_id', 'title', 'slug', 'page_type', 'status',
        'seo_title', 'seo_description', 'show_in_nav', 'nav_sort',
    ];

    protected function casts(): array
    {
        return [
            'show_in_nav' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $page) {
            if (blank($page->slug) && filled($page->title)) {
                $page->slug = Str::slug($page->title);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(CompanyWebsiteBlock::class)->orderBy('sort_order');
    }

    public function enabledBlocks(): HasMany
    {
        return $this->blocks()->where('is_enabled', true);
    }
}
