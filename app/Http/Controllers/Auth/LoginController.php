<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use App\Services\LoginGreetingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect($this->dashboardRoute(Auth::user()));
        }

        return view('auth.login', ['panel' => 'company']);
    }

    public function showPlatformLoginForm()
    {
        if (Auth::check()) {
            return redirect($this->dashboardRoute(Auth::user()));
        }

        return view('auth.login', ['panel' => 'platform']);
    }

    public function login(Request $request)
    {
        return $this->attemptLogin($request, ['admin', 'sub-admin'], 'company');
    }

    public function loginPlatform(Request $request)
    {
        return $this->attemptLogin($request, ['super-admin'], 'platform');
    }

    protected function attemptLogin(Request $request, array $allowedRoles, string $panel)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => __('Your account has been deactivated.'),
            ]);
        }

        if (! in_array($user->role?->slug, $allowedRoles, true)) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => $panel === 'platform'
                    ? __('This account cannot access Platform Admin. Use the institute login instead.')
                    : __('This account cannot access the Institute Panel. Use the platform admin login at /admin/login.'),
            ]);
        }

        $user->update(['last_login_at' => now()]);
        ActivityLogger::log('login', "User {$user->name} logged in ({$panel} panel)", $user);
        $request->session()->regenerate();

        if ($panel === 'company') {
            LoginGreetingService::flashForUser($user);
        }

        return redirect()->intended($this->dashboardRoute($user));
    }

    public function logout(Request $request)
    {
        $wasPlatform = $request->user()?->isSuperAdmin() ?? false;
        $wasLearner = $request->user()?->isLearner() ?? false;

        ActivityLogger::log('logout', 'User logged out');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($wasPlatform) {
            return redirect()->route('platform.login');
        }

        if ($wasLearner) {
            return redirect()->route('student.login');
        }

        return redirect()->route('login');
    }

    protected function dashboardRoute($user): string
    {
        return match ($user->role?->slug) {
            'super-admin' => route('platform.dashboard'),
            'admin', 'sub-admin' => route('admin.dashboard'),
            'instructor' => route('instructor.dashboard'),
            default => route('learner.dashboard'),
        };
    }
}
