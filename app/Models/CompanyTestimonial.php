<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyTestimonial extends Model
{
    protected $fillable = [
        'company_id', 'author_name', 'author_title', 'content', 'rating', 'avatar', 'is_published', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'rating' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function avatarUrl(): string
    {
        return Company::mediaUrl($this->avatar);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
