<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageEvent extends Model
{
    protected $fillable = [
        'landing_page_id', 'event_type', 'ip_address', 'user_agent', 'meta',
    ];

    protected function casts(): array
    {
        return ['meta' => 'array'];
    }

    public function page()
    {
        return $this->belongsTo(LandingPage::class, 'landing_page_id');
    }
}
