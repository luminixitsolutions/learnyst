<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class InstructorTrack extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'instructor_id',
        'content_security',
        'status',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (InstructorTrack $track) {
            if (empty($track->slug)) {
                $track->slug = Str::slug(Str::limit($track->title, 40, '')) . '-' . Str::random(4);
            }
        });
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function contentSecurityLabel(): string
    {
        return match ($this->content_security) {
            'no_encryption' => 'No Encryption',
            default => 'Encryption',
        };
    }
}
