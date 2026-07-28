<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use App\Services\AuthSecurityService;
use App\Services\LoginGreetingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __construct(protected AuthSecurityService $security) {}

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
        return $this->attemptLogin($request, ['admin', 'sub-admin', 'counselor'], 'company');
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
            $this->security->recordLogin(null, $request, 'failed', 'password', $credentials['email']);
            ActivityLogger::logAs(null, 'login_failed', "Failed login attempt for {$credentials['email']}", null, [
                'email' => $credentials['email'],
                'panel' => $panel,
                'provider' => 'password',
            ]);
            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            $this->security->recordLogin($user, $request, 'failed', 'password');
            ActivityLogger::logAs($user, 'login_failed', "Login blocked — deactivated account ({$user->email})", $user, [
                'panel' => $panel,
                'reason' => 'deactivated',
            ]);
            throw ValidationException::withMessages([
                'email' => __('Your account has been deactivated.'),
            ]);
        }

        if (! in_array($user->role?->slug, $allowedRoles, true)) {
            Auth::logout();
            $this->security->recordLogin($user, $request, 'failed', 'password');
            ActivityLogger::logAs($user, 'login_failed', "Login blocked — wrong panel for {$user->email}", $user, [
                'panel' => $panel,
                'reason' => 'wrong_panel',
            ]);
            throw ValidationException::withMessages([
                'email' => $panel === 'platform'
                    ? __('This account cannot access Platform Admin. Use the institute login instead.')
                    : __('This account cannot access the Institute Panel. Use the platform admin login at /admin/login.'),
            ]);
        }

        $companyId = $panel === 'platform' ? null : ($user->isAdmin() ? $user->id : $user->created_by);
        if (! $this->security->ipAllowed($companyId ? (int) $companyId : null, $request->ip() ?: '0.0.0.0', $panel === 'platform')) {
            Auth::logout();
            $this->security->recordLogin($user, $request, 'blocked', 'password');
            ActivityLogger::logAs($user, 'login_blocked', "Login blocked by IP policy for {$user->email}", $user, [
                'panel' => $panel,
                'ip' => $request->ip(),
            ]);
            throw ValidationException::withMessages([
                'email' => __('Access from this IP address is not allowed.'),
            ]);
        }

        if ($user->two_factor_enabled) {
            Auth::logout();
            $request->session()->put([
                '2fa:user:id' => $user->id,
                '2fa:remember' => $request->boolean('remember'),
                '2fa:panel' => $panel,
            ]);
            $this->security->recordLogin($user, $request, '2fa_required', 'password');
            ActivityLogger::logAs($user, '2fa_required', "2FA required for {$user->email}", $user, [
                'panel' => $panel,
            ]);

            return redirect()->route('auth.2fa.challenge');
        }

        $user->update(['last_login_at' => now()]);
        ActivityLogger::log('login', "User {$user->name} logged in ({$panel} panel)", $user, [
            'panel' => $panel,
            'provider' => 'password',
        ]);
        $request->session()->regenerate();
        $cookie = $this->security->afterSuccessfulLogin($user, $request, 'password');

        if ($panel === 'company') {
            LoginGreetingService::flashForUser($user);
        }

        return redirect()->intended($this->dashboardRoute($user))->cookie($cookie);
    }

    public function logout(Request $request)
    {
        $wasPlatform = $request->user()?->isSuperAdmin() ?? false;
        $wasLearner = $request->user()?->isStudentPanelUser() ?? false;
        $user = $request->user();

        ActivityLogger::log('logout', $user ? "User {$user->name} logged out" : 'User logged out', $user);

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
            'admin', 'sub-admin', 'counselor' => route('admin.dashboard'),
            'instructor' => route('instructor.dashboard'),
            'alumni' => route('alumni.dashboard'),
            'parent' => route('parent.dashboard'),
            default => route('learner.dashboard'),
        };
    }
}
