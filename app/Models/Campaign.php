<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [
        'title',
        'subject',
        'content',
        'meta',
        'channel',
        'status',
        'audience_count',
        'sent_count',
        'failed_count',
        'scheduled_at',
        'sent_at',
        'created_by',
        'segment_id',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function segment()
    {
        return $this->belongsTo(Segment::class);
    }

    public function sends()
    {
        return $this->hasMany(CampaignSend::class);
    }
}
