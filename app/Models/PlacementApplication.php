<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PlacementApplication extends Model
{
    protected $fillable = [
        'placement_job_id', 'user_id', 'created_by', 'status', 'resume_path',
        'cover_letter', 'resume_data', 'interview_at', 'interview_mode', 'interview_notes',
    ];

    protected function casts(): array
    {
        return [
            'resume_data' => 'array',
            'interview_at' => 'datetime',
        ];
    }

    public function job()
    {
        return $this->belongsTo(PlacementJob::class, 'placement_job_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resumeUrl(): ?string
    {
        return $this->resume_path ? Storage::disk('public')->url($this->resume_path) : null;
    }
}
