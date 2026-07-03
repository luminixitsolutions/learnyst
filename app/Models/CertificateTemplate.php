<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    protected $fillable = [
        'name', 'certificate_title', 'html_content', 'background_image', 'layout_config',
        'signature_image', 'seal_image', 'is_default', 'status',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'layout_config' => 'array',
        ];
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
}
