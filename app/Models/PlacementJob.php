<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlacementJob extends Model
{
    protected $fillable = [
        'created_by', 'placement_company_id', 'title', 'type', 'location',
        'employment_type', 'stipend_or_salary', 'description', 'requirements',
        'closes_at', 'status',
    ];

    protected function casts(): array
    {
        return [
            'closes_at' => 'date',
            'stipend_or_salary' => 'decimal:2',
        ];
    }

    public function company()
    {
        return $this->belongsTo(PlacementCompany::class, 'placement_company_id');
    }

    public function applications()
    {
        return $this->hasMany(PlacementApplication::class);
    }
}
