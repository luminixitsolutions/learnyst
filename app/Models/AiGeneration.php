<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiGeneration extends Model
{
    protected $fillable = [
        'created_by', 'user_id', 'feature', 'title', 'prompt', 'output',
        'status', 'course_id', 'meta', 'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'reviewed_at' => 'datetime',
        ];
    }

    public static function features(): array
    {
        return [
            'course_outline' => 'Course outline generator',
            'quiz' => 'Quiz / MCQ generator',
            'notes' => 'Notes generator',
            'assignment' => 'Assignment generator',
            'doubt_chat' => 'Doubt chat',
            'study_planner' => 'Study planner',
            'performance' => 'Performance suggestions',
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
}
