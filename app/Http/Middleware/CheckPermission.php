<?php

namespace App\Http\Middleware;

use App\Services\PermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $module, string $action = 'view'): Response
    {
        $user = $request->user();

        if (!$user || !PermissionService::hasPermission($user, $module, $action)) {
            abort(403, 'You do not have permission to perform this action.');
        }

        return $next($request);
    }
}
