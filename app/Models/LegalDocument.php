<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegalDocument extends Model
{
    protected $fillable = [
        'created_by',
        'title',
        'document_type',
        'content',
        'version',
        'effective_date',
        'status',
    ];

    protected function casts(): array
    {
        return ['effective_date' => 'date'];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function typeLabel(): string
    {
        return match ($this->document_type) {
            'privacy_policy' => 'Privacy Policy',
            'terms_of_service' => 'Terms of Service',
            'refund_policy' => 'Refund Policy',
            'user_agreement' => 'User Agreement',
            default => 'Other',
        };
    }
}
