<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LandingPage extends Model
{
    protected $fillable = [
        'created_by', 'title', 'slug', 'headline', 'body', 'cta_text', 'cta_url',
        'hero_image', 'blocks', 'is_published', 'views', 'cta_clicks', 'leads_captured',
    ];

    protected function casts(): array
    {
        return [
            'blocks' => 'array',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (LandingPage $page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title).'-'.Str::lower(Str::random(4));
            }
        });
    }

    public function events()
    {
        return $this->hasMany(LandingPageEvent::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
