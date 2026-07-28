<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'created_by', 'name', 'code', 'city', 'address', 'phone',
        'is_active', 'revenue_share_percent',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'revenue_share_percent' => 'decimal:2',
        ];
    }

    public function admins()
    {
        return $this->belongsToMany(User::class, 'branch_admins')->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'branch_user')
            ->withPivot('role_in_branch')
            ->withTimestamps();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }
}
