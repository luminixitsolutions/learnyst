<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebinarRegistration extends Model
{
    protected $fillable = [
        'webinar_id', 'created_by', 'name', 'email', 'phone',
        'status', 'confirmed_at', 'reminder_sent_at', 'lead_id', 'meta',
    ];

    protected function casts(): array
    {
        return [
            'confirmed_at' => 'datetime',
            'reminder_sent_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function webinar()
    {
        return $this->belongsTo(Webinar::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
