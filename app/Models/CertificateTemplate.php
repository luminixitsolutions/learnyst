<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    protected $fillable = [
        'course_id', 'name', 'certificate_title', 'html_content', 'background_image', 'layout_config',
        'signature_image', 'seal_image', 'is_default', 'status', 'created_by',
        'validity_years', 'validity_days', 'renewal_price', 'requires_renewal_assessment',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'layout_config' => 'array',
            'validity_years' => 'integer',
            'validity_days' => 'integer',
            'renewal_price' => 'decimal:2',
            'requires_renewal_assessment' => 'boolean',
        ];
    }

    public function hasValidityPeriod(): bool
    {
        return (bool) ($this->validity_years || $this->validity_days);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
}
