<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Community extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'cover_image',
        'category_id', 'created_by', 'is_active', 'requires_approval',
        'telegram_invite_url', 'telegram_chat_id', 'telegram_push_enabled',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'requires_approval' => 'boolean',
            'telegram_push_enabled' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Community $community) {
            if (empty($community->slug)) {
                $community->slug = Str::slug($community->name);
            }
        });
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'community_members')->withPivot('role');
    }

    public function posts()
    {
        return $this->hasMany(CommunityPost::class);
    }

    public function announcements()
    {
        return $this->hasMany(CommunityAnnouncement::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
