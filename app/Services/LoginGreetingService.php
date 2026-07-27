<?php

namespace App\Services;

use App\Models\User;

class LoginGreetingService
{
    public static function firstName(User $user): string
    {
        $name = trim((string) $user->name);

        if ($name === '') {
            return 'there';
        }

        $parts = preg_split('/\s+/', $name);

        return $parts[0] ?: $name;
    }

    public static function flashForUser(User $user): void
    {
        session()->flash('login_greeting_name', self::firstName($user));
    }
}
