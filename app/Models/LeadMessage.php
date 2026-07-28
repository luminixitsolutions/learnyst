<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadMessage extends Model
{
    protected $fillable = [
        'lead_id', 'user_id', 'channel', 'direction',
        'subject', 'body', 'status', 'sent_at',
    ];

    protected function casts(): array
    {
        return ['sent_at' => 'datetime'];
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
