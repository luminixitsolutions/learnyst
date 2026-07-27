<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'certificate_number', 'user_id', 'course_id', 'batch_id',
        'certificate_template_id', 'issued_at', 'status', 'expires_at',
        'renewed_from_id', 'renewal_count', 'last_reminder_at', 'last_reminder_days',
        'pdf_path',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'expires_at' => 'datetime',
            'last_reminder_at' => 'datetime',
            'renewal_count' => 'integer',
            'last_reminder_days' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Certificate $cert) {
            if (empty($cert->certificate_number)) {
                $cert->certificate_number = 'CERT-' . strtoupper(uniqid());
            }

            if (empty($cert->status)) {
                $cert->status = 'valid';
            }
        });

        static::created(function (Certificate $cert) {
            if ($cert->expires_at || ! $cert->certificate_template_id) {
                return;
            }

            app(\App\Services\CertificateLifecycleService::class)->applyLifecycle($cert);
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

    public function renewedFrom()
    {
        return $this->belongsTo(self::class, 'renewed_from_id');
    }

    public function renewals()
    {
        return $this->hasMany(self::class, 'renewed_from_id');
    }

    public function isExpired(): bool
    {
        return in_array($this->status, ['expired', 'renewal_due'], true)
            || ($this->expires_at && $this->expires_at->isPast());
    }

    public function needsRenewal(): bool
    {
        return in_array($this->status, ['expiring_soon', 'expired', 'renewal_due'], true);
    }
}
