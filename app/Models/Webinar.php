<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Webinar extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'is_free',
        'content_security',
        'status',
        'starts_at',
        'registration_enabled',
        'reminder_hours_before',
        'confirmation_message',
        'meeting_url',
        'sort_order',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_free' => 'boolean',
            'starts_at' => 'datetime',
            'registration_enabled' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Webinar $webinar) {
            if (empty($webinar->slug)) {
                $webinar->slug = Str::slug(Str::limit($webinar->title, 40, '')) . '-' . Str::random(4);
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrations()
    {
        return $this->hasMany(WebinarRegistration::class);
    }

    public function contentSecurityLabel(): string
    {
        return match ($this->content_security) {
            'no_encryption' => 'No Encryption',
            default => 'Encryption',
        };
    }
}
