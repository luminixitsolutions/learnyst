<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyWebsiteMenu extends Model
{
    protected $fillable = [
        'company_id', 'location', 'label', 'url', 'page_id', 'sort_order', 'is_enabled',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(CompanyWebsitePage::class, 'page_id');
    }

    public function resolvedUrl(): string
    {
        if ($this->page_id) {
            $this->loadMissing(['page', 'company']);
            if ($this->page && $this->company) {
                return route('website.companies.page', [
                    'slug' => $this->company->slug,
                    'pageSlug' => $this->page->slug,
                ]);
            }
        }

        return $this->url ?: '#';
    }
}
