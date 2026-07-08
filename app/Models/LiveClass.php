<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveClass extends Model
{
    public const TYPES = [
        'super_live' => 'Super Live',
        'learnyst_meeting' => 'Learnyst Meeting',
        'learnyst_webinar' => 'Learnyst Webinar',
        'embed_live_class' => 'Embed Live Class',
    ];

    public const RECORDING_LAYOUTS = [
        'host_only' => 'Record only host screen or camera',
        'host_screen_and_camera' => 'Record both host screen and camera',
        'host_and_participants' => 'Record host screen or camera and participants',
        'grid_view' => 'Grid View - All participants displayed equally',
    ];

    protected $fillable = [
        'course_lesson_id', 'live_class_type', 'super_live_capacity',
        'starts_at', 'duration_hours', 'duration_minutes', 'recording_layout_mode',
        'embed_url', 'new_recording', 'enable_participant_list', 'chat_box', 'enable_qa', 'show_whiteboard',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'super_live_capacity' => 'integer',
            'duration_hours' => 'integer',
            'duration_minutes' => 'integer',
            'new_recording' => 'boolean',
            'enable_participant_list' => 'boolean',
            'chat_box' => 'boolean',
            'enable_qa' => 'boolean',
            'show_whiteboard' => 'boolean',
        ];
    }

    public function lesson()
    {
        return $this->belongsTo(CourseLesson::class, 'course_lesson_id');
    }
}
