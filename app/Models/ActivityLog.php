<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'company_id', 'action', 'subject_type', 'subject_id',
        'description', 'properties', 'ip_address', 'user_agent',
    ];

    protected function casts(): array
    {
        return ['properties' => 'array'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function subject()
    {
        return $this->morphTo();
    }

    public function subjectLabel(): string
    {
        if (! $this->subject_type) {
            return '—';
        }

        $base = class_basename($this->subject_type);

        return $base.($this->subject_id ? ' #'.$this->subject_id : '');
    }

    public static function authActionTypes(): array
    {
        return [
            'login',
            'login_failed',
            'login_blocked',
            'login_google',
            'login_facebook',
            'login_linkedin',
            'login_apple',
            'logout',
            '2fa_required',
            '2fa_failed',
            'platform_impersonation_started',
            'platform_impersonation_ended',
            'user_force_logout',
        ];
    }
}
