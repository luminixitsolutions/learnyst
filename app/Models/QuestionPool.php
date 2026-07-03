<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class QuestionPool extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'status',
        'questions_count',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (QuestionPool $pool) {
            if (empty($pool->slug)) {
                $pool->slug = Str::slug(Str::limit($pool->title, 40, '')) . '-' . Str::random(4);
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
