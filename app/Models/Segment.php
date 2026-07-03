<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Segment extends Model
{
    protected $fillable = ['title', 'slug', 'description', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::creating(function (Segment $segment) {
            if (empty($segment->slug)) {
                $segment->slug = Str::slug($segment->title);
            }
        });
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'segment_user');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'segment_course');
    }
}
