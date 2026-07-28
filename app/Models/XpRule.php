<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class XpRule extends Model
{
    protected $fillable = [
        'created_by', 'action_key', 'label', 'points', 'daily_cap', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public static function defaultRules(): array
    {
        return [
            ['action_key' => 'lesson_complete', 'label' => 'Lesson completed', 'points' => 10, 'daily_cap' => null],
            ['action_key' => 'quiz_pass', 'label' => 'Quiz passed', 'points' => 25, 'daily_cap' => null],
            ['action_key' => 'live_attendance', 'label' => 'Live class attendance', 'points' => 15, 'daily_cap' => null],
            ['action_key' => 'assignment_submit', 'label' => 'Assignment submitted', 'points' => 20, 'daily_cap' => null],
            ['action_key' => 'login_streak', 'label' => 'Daily login streak', 'points' => 5, 'daily_cap' => 1],
        ];
    }
}
