<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseEnquiry extends Model
{
    protected $fillable = [
        'course_id', 'name', 'email', 'phone', 'subject', 'message', 'status',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
