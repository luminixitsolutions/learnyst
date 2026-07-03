<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'question_pool_id',
        'question_text',
        'question_type',
        'options',
        'correct_answer',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
        ];
    }

    public function questionPool()
    {
        return $this->belongsTo(QuestionPool::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function typeLabel(): string
    {
        return match ($this->question_type) {
            'true_false' => 'True / False',
            'essay' => 'Essay',
            'fill_blank' => 'Fill in the Blank',
            default => 'MCQ',
        };
    }
}
