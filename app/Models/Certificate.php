<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'certificate_number', 'user_id', 'course_id', 'batch_id',
        'certificate_template_id', 'issued_at', 'pdf_path',
    ];

    protected function casts(): array
    {
        return ['issued_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(function (Certificate $cert) {
            if (empty($cert->certificate_number)) {
                $cert->certificate_number = 'CERT-' . strtoupper(uniqid());
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function template()
    {
        return $this->belongsTo(CertificateTemplate::class, 'certificate_template_id');
    }
}
