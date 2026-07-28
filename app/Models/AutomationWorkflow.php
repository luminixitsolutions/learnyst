<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationWorkflow extends Model
{
    protected $fillable = [
        'created_by', 'name', 'trigger_key', 'trigger_config',
        'actions', 'is_active', 'run_count', 'last_run_at',
    ];

    protected function casts(): array
    {
        return [
            'trigger_config' => 'array',
            'actions' => 'array',
            'is_active' => 'boolean',
            'last_run_at' => 'datetime',
        ];
    }

    public static function triggers(): array
    {
        return [
            'signup' => 'Learner signup',
            'abandoned_checkout' => 'Abandoned checkout',
            'course_incomplete' => 'Course incomplete',
            'exam_result' => 'Exam result',
            'webinar_registration' => 'Webinar registration',
            'birthday' => 'Birthday',
            'inactivity' => 'Inactivity',
        ];
    }

    public static function actionTypes(): array
    {
        return [
            'send_email' => 'Send email',
            'send_sms' => 'Send SMS',
            'send_whatsapp' => 'Send WhatsApp',
            'create_follow_up' => 'Create follow-up task',
            'add_segment' => 'Add to segment',
            'award_coupon' => 'Award coupon (note)',
        ];
    }

    public function runs()
    {
        return $this->hasMany(AutomationRun::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
