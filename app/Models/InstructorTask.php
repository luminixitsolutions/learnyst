<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstructorTask extends Model
{
    protected $fillable = [
        'user_id', 'course_id', 'batch_id', 'title',
        'description', 'status', 'due_date',
    ];

    protected function casts(): array
    {
        return ['due_date' => 'date'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}
