<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Poll extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'poll_type',
        'description',
        'status',
        'tags',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Poll $poll) {
            if (empty($poll->slug)) {
                $poll->slug = Str::slug(Str::limit($poll->title, 40, '')) . '-' . Str::random(4);
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pollTypeLabel(): string
    {
        return config('poll-types.' . $this->poll_type, $this->poll_type);
    }
}
