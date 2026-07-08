<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseSection extends Model
{
    protected $fillable = ['course_id', 'title', 'description', 'sort_order', 'status', 'drip_days', 'drip_date'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lessons()
    {
        return $this->hasMany(CourseLesson::class)->orderBy('sort_order');
    }
}
