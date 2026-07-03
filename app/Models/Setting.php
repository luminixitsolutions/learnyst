<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['group', 'key', 'value', 'type'];

    public static function get(string $key, mixed $default = null, string $group = 'general'): mixed
    {
        $setting = Cache::remember("setting.{$group}.{$key}", 3600, function () use ($key, $group) {
            return static::where('group', $group)->where('key', $key)->first();
        });

        return $setting?->value ?? $default;
    }

    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'text'): void
    {
        static::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => $value, 'type' => $type]
        );
        Cache::forget("setting.{$group}.{$key}");
    }
}
