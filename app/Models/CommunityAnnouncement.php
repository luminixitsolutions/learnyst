<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityAnnouncement extends Model
{
    protected $fillable = [
        'community_id', 'created_by', 'title', 'body',
        'pushed_to_telegram', 'telegram_pushed_at', 'meta',
    ];

    protected function casts(): array
    {
        return [
            'pushed_to_telegram' => 'boolean',
            'telegram_pushed_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
