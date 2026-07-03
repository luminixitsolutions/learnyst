<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'visibility'];

    protected static function booted(): void
    {
        static::creating(function (Tag $tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class);
    }

    public function visibilityLabel(): string
    {
        return match ($this->visibility) {
            'private' => 'Private',
            'classification' => 'Classification',
            default => 'Public',
        };
    }
}
