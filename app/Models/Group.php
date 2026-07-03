<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Group extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_active', 'created_by'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::creating(function (Group $group) {
            if (empty($group->slug)) {
                $group->slug = Str::slug($group->name);
            }
        });
    }

    public function learners()
    {
        return $this->belongsToMany(User::class, 'group_user');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'group_course');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
