<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadCallLog extends Model
{
    protected $fillable = [
        'lead_id', 'user_id', 'direction', 'outcome',
        'duration_seconds', 'notes', 'called_at',
    ];

    protected function casts(): array
    {
        return ['called_at' => 'datetime'];
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
