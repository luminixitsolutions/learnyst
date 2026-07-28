<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyWebsiteBlock extends Model
{
    protected $fillable = [
        'company_website_page_id', 'block_type', 'title', 'content', 'is_enabled', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'is_enabled' => 'boolean',
        ];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(CompanyWebsitePage::class, 'company_website_page_id');
    }

    public static function blockTypes(): array
    {
        return [
            'hero' => 'Hero / Banner',
            'text' => 'Text',
            'cta' => 'Call to action',
            'testimonials' => 'Testimonials',
            'faculty' => 'Faculty grid',
            'faq' => 'FAQ accordion',
            'gallery' => 'Gallery',
            'form' => 'Contact form',
            'newsletter' => 'Newsletter',
            'pricing' => 'Pricing',
            'courses' => 'Course showcase',
        ];
    }
}
