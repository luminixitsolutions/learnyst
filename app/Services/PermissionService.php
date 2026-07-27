<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class PermissionService
{
    public static function modules(): array
    {
        return [
            'dashboard' => 'Dashboard',
            'learners' => 'Learners',
            'products' => 'Products',
            'sales' => 'Sales',
            'batches' => 'Batches',
            'reports' => 'Reports',
            'insights' => 'Insights',
            'settings' => 'Settings',
            'community' => 'Community',
            'instructors' => 'Instructors',
            'sub_admins' => 'Sub Admins',
            'alumni' => 'Alumni Network',
            'certificates_renewal' => 'Certificate Renewal',
            'proctoring' => 'Exam Proctoring',
            'parent' => 'Parent Portal',
            'compliance' => 'Compliance Center',
            'notifications' => 'Notification Center',
        ];
    }

    /**
     * Default permission slugs synced when seeding non-admin roles.
     *
     * @return array<string, list<string>>
     */
    public static function defaultRolePermissions(): array
    {
        return [
            'counselor' => [
                'dashboard.view',
                'learners.view',
                'learners.export',
                'reports.view',
                'sales.view',
            ],
            'sub-admin' => [
                'dashboard.view',
                'learners.view',
                'products.view',
                'sales.view',
            ],
        ];
    }

    public static function actions(): array
    {
        return ['view', 'add', 'edit', 'delete', 'export', 'manage'];
    }

    public static function hasPermission(?User $user, string $module, string $action = 'view'): bool
    {
        if (!$user || !$user->role) {
            return false;
        }

        if (in_array($user->role->slug, ['admin'], true)) {
            return true;
        }

        $slug = "{$module}.{$action}";

        return Cache::remember("user.{$user->id}.perm.{$slug}", 300, function () use ($user, $slug) {
            if ($user->permissions()->where('slug', $slug)->exists()) {
                return true;
            }

            return $user->role->permissions()->where('slug', $slug)->exists();
        });
    }

    public static function canAccessRoute(?User $user, ?string $permission): bool
    {
        if (!$permission) {
            return true;
        }

        if (!$user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        [$module, $action] = array_pad(explode('.', $permission, 2), 2, 'view');

        return self::hasPermission($user, $module, $action);
    }
}
