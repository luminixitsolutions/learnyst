<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Model
{
    protected $fillable = [
        'user_id', 'course_id', 'batch_id', 'bundle_id', 'order_id',
        'enrollment_type', 'status', 'access_type', 'amount', 'meta', 'show_custom_fields',
        'enrolled_at', 'access_starts_at', 'expires_at', 'progress', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'enrolled_at' => 'datetime',
            'access_starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'completed_at' => 'datetime',
            'progress' => 'decimal:2',
            'amount' => 'decimal:2',
            'meta' => 'array',
            'show_custom_fields' => 'boolean',
        ];
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

    public function bundle()
    {
        return $this->belongsTo(Bundle::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
