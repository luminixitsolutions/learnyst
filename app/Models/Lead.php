<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'created_by',
        'name',
        'email',
        'phone',
        'source',
        'course_id',
        'assigned_to',
        'converted_user_id',
        'converted_at',
        'status',
        'stage',
        'notes',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'converted_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function convertedUser()
    {
        return $this->belongsTo(User::class, 'converted_user_id');
    }

    public function followUps()
    {
        return $this->hasMany(LeadFollowUp::class);
    }

    public function callLogs()
    {
        return $this->hasMany(LeadCallLog::class);
    }

    public function leadNotes()
    {
        return $this->hasMany(LeadNote::class);
    }

    public function messages()
    {
        return $this->hasMany(LeadMessage::class);
    }

    public static function stages(): array
    {
        return [
            'new' => 'New',
            'contacted' => 'Contacted',
            'counseling' => 'Counseling',
            'documents' => 'Documents',
            'admitted' => 'Admitted',
            'lost' => 'Lost',
        ];
    }

    public function isConverted(): bool
    {
        return $this->status === 'converted' || filled($this->converted_user_id);
    }
}
