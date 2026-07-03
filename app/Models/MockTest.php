<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MockTest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'price',
        'is_free',
        'quiz_type',
        'template',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_free' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (MockTest $mockTest) {
            if (empty($mockTest->slug)) {
                $mockTest->slug = Str::slug(Str::limit($mockTest->title, 40, '')) . '-' . Str::random(4);
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function quizTypeLabel(): string
    {
        return match ($this->quiz_type) {
            'offline' => 'Offline Quiz',
            default => 'Online Quiz',
        };
    }

    public function templateLabel(): string
    {
        if (!$this->template) {
            return '—';
        }

        return config('mock-test-templates.' . $this->template, $this->template);
    }
}
