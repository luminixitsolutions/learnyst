<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseCertificateCriterion extends Model
{
    protected $fillable = [
        'course_id', 'criterion_type', 'logic', 'is_mandatory', 'config', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_mandatory' => 'boolean',
            'config' => 'array',
            'sort_order' => 'integer',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
