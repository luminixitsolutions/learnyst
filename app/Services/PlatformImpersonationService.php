<?php

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PlatformImpersonationService
{
    public const SESSION_KEY = 'platform_impersonator_id';

    public static function isActive(): bool
    {
        return session()->has(self::SESSION_KEY);
    }

    public static function impersonatorId(): ?int
    {
        $id = session(self::SESSION_KEY);

        return is_numeric($id) ? (int) $id : null;
    }

    public static function enter(Company $company): User
    {
        $actor = Auth::user();
        abort_unless($actor?->isSuperAdmin(), 403);

        $owner = $company->owner;
        if (! $owner) {
            throw ValidationException::withMessages([
                'company' => 'This institute has no owner account linked.',
            ]);
        }

        if (! $owner->is_active) {
            throw ValidationException::withMessages([
                'company' => 'The institute owner account is inactive.',
            ]);
        }

        if (! ($company->is_active ?? true)) {
            throw ValidationException::withMessages([
                'company' => 'This institute is suspended.',
            ]);
        }

        if (! in_array($owner->role?->slug, ['admin', 'sub-admin', 'counselor'], true)) {
            throw ValidationException::withMessages([
                'company' => 'The institute owner cannot access the company panel.',
            ]);
        }

        session()->put(self::SESSION_KEY, $actor->id);
        Auth::login($owner);
        session()->regenerate();

        ActivityLogger::log(
            'platform_impersonation_started',
            "Platform admin entered institute panel: {$company->name}",
            $company
        );

        return $owner;
    }

    public static function exit(): void
    {
        $impersonatorId = self::impersonatorId();
        abort_unless($impersonatorId, 403);

        $impersonator = User::query()->find($impersonatorId);
        abort_unless($impersonator?->isSuperAdmin(), 403);

        session()->forget(self::SESSION_KEY);
        Auth::login($impersonator);
        session()->regenerate();

        ActivityLogger::log('platform_impersonation_ended', 'Platform admin returned to platform panel', $impersonator);
    }
}
