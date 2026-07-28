<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(string $action, ?string $description = null, ?Model $subject = null, array $properties = []): void
    {
        $user = Auth::user();
        $request = request();

        ActivityLog::create([
            'user_id' => $user?->id,
            'company_id' => self::resolveCompanyId($user, $subject, $properties),
            'action' => $action,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->getKey(),
            'description' => $description,
            'properties' => $properties ?: null,
            'ip_address' => $request?->ip(),
            'user_agent' => $request ? substr((string) $request->userAgent(), 0, 500) : null,
        ]);
    }

    /**
     * Log without requiring an authenticated actor (failed logins, etc.).
     */
    public static function logAs(?User $actor, string $action, ?string $description = null, ?Model $subject = null, array $properties = []): void
    {
        $request = request();

        ActivityLog::create([
            'user_id' => $actor?->id,
            'company_id' => self::resolveCompanyId($actor, $subject, $properties),
            'action' => $action,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->getKey(),
            'description' => $description,
            'properties' => $properties ?: null,
            'ip_address' => $request?->ip(),
            'user_agent' => $request ? substr((string) $request->userAgent(), 0, 500) : null,
        ]);
    }

    protected static function resolveCompanyId(?User $user, ?Model $subject, array $properties): ?int
    {
        if (! empty($properties['company_id']) && is_numeric($properties['company_id'])) {
            return (int) $properties['company_id'];
        }

        if ($subject instanceof Company) {
            return (int) $subject->id;
        }

        if (! $user) {
            return null;
        }

        if ($user->company?->id) {
            return (int) $user->company->id;
        }

        $owned = Company::where('owner_user_id', $user->id)->value('id');
        if ($owned) {
            return (int) $owned;
        }

        if ($user->created_by) {
            $parent = Company::where('owner_user_id', $user->created_by)->value('id');
            if ($parent) {
                return (int) $parent;
            }
        }

        return null;
    }
}
