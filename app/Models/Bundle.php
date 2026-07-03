<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Bundle extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'description', 'price', 'sale_price', 'validity_days', 'status',
        'thumbnail', 'created_by', 'enrollment_count',
    ];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'sale_price' => 'decimal:2', 'validity_days' => 'integer'];
    }

    protected static function booted(): void
    {
        static::creating(function (Bundle $bundle) {
            if (empty($bundle->slug)) {
                $bundle->slug = Str::slug($bundle->title) . '-' . Str::random(4);
            }
        });
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'bundle_courses')->withPivot('sort_order')->orderByPivot('sort_order');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class, 'bundle_id');
    }
}
