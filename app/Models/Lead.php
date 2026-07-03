<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'source', 'course_id', 'status', 'notes'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
