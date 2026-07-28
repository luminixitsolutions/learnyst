<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveClassAttendance extends Model
{
    protected $fillable = [
        'scheduled_event_id', 'user_id', 'marked_by', 'attended_at',
    ];

    protected function casts(): array
    {
        return ['attended_at' => 'datetime'];
    }

    public function event()
    {
        return $this->belongsTo(ScheduledEvent::class, 'scheduled_event_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
