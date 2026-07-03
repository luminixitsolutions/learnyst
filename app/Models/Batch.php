<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Batch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id', 'instructor_id', 'title', 'slug', 'description',
        'price', 'is_free', 'start_date', 'end_date', 'is_online', 'template', 'quiz_type', 'status', 'max_learners',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_online' => 'boolean',
            'is_free' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Batch $batch) {
            if (empty($batch->slug)) {
                $batch->slug = Str::slug($batch->title) . '-' . Str::random(4);
            }
        });
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function learners()
    {
        return $this->belongsToMany(User::class, 'batch_learners')->withPivot('status', 'progress');
    }
}
