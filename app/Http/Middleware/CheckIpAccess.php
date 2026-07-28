<?php

namespace App\Http\Middleware;

use App\Services\AuthSecurityService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckIpAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (! $user) {
            return $next($request);
        }

        $security = app(AuthSecurityService::class);
        $ip = $request->ip() ?: '0.0.0.0';

        if ($user->isSuperAdmin()) {
            if (! $security->ipAllowed(null, $ip, true)) {
                Auth::logout();
                abort(403, 'Your IP is not allowed for Platform Admin.');
            }

            return $next($request);
        }

        $companyId = $user->isAdmin() ? $user->id : $user->created_by;
        if ($companyId && ! $security->ipAllowed((int) $companyId, $ip, false)) {
            $security->recordLogin($user, $request, 'blocked');
            Auth::logout();
            abort(403, 'Your IP address is not allowed for this institute.');
        }

        return $next($request);
    }
}
