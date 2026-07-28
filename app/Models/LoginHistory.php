<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    protected $fillable = [
        'user_id', 'email', 'ip_address', 'user_agent', 'device_id', 'status', 'provider',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
