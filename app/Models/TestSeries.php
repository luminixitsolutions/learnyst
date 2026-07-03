<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TestSeries extends Model
{
    use SoftDeletes;

    protected $table = 'test_series';

    protected $fillable = [
        'title',
        'slug',
        'price',
        'is_free',
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
        static::creating(function (TestSeries $testSeries) {
            if (empty($testSeries->slug)) {
                $testSeries->slug = Str::slug(Str::limit($testSeries->title, 40, '')) . '-' . Str::random(4);
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sections()
    {
        return $this->hasMany(TestSeriesSection::class)->orderBy('sort_order');
    }
}
