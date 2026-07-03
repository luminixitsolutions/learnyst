<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id', 'created_by', 'title', 'subtitle', 'slug', 'description', 'thumbnail',
        'intro_video_url', 'product_type', 'price', 'sale_price', 'is_free', 'access_type',
        'start_date', 'expiry_date', 'validity_days', 'status', 'drip_enabled', 'meta',
        'seo_title', 'seo_description', 'enrollment_count',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'validity_days' => 'integer',
            'is_free' => 'boolean',
            'drip_enabled' => 'boolean',
            'start_date' => 'date',
            'expiry_date' => 'date',
            'meta' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Course $course) {
            if (empty($course->slug)) {
                $course->slug = Str::slug($course->title) . '-' . Str::random(4);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sections()
    {
        return $this->hasMany(CourseSection::class)->orderBy('sort_order');
    }

    public function lessons()
    {
        return $this->hasManyThrough(CourseLesson::class, CourseSection::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function instructors()
    {
        return $this->belongsToMany(User::class, 'course_instructors')->withPivot('is_primary');
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function segments()
    {
        return $this->belongsToMany(Segment::class, 'segment_course');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('product_type', $type);
    }
}
