<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpAccessRule extends Model
{
    protected $fillable = [
        'created_by', 'scope', 'rule_type', 'ip_cidr', 'label', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
