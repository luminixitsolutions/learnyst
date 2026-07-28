<?php

namespace App\Http\Middleware;

use App\Services\AuthSecurityService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureDeviceNotRevoked
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if ($user && app(AuthSecurityService::class)->isDeviceRevoked($user, $request)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'This device session was revoked. Please sign in again.',
            ]);
        }

        return $next($request);
    }
}
