<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyTeamMember extends Model
{
    protected $fillable = [
        'company_id', 'name', 'role', 'bio', 'photo', 'is_published', 'sort_order',
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

    public function photoUrl(): string
    {
        return Company::mediaUrl($this->photo);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
