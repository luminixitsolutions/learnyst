<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Services\ActivityLogger;
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

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => __('Your account has been deactivated.'),
            ]);
        }

        $user->update(['last_login_at' => now()]);
        ActivityLogger::log('login', "User {$user->name} logged in", $user);
        $request->session()->regenerate();

        return redirect()->intended($this->dashboardRoute($user));
    }

    public function logout(Request $request)
    {
        ActivityLogger::log('logout', 'User logged out');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function dashboardRoute($user): string
    {
        return match ($user->role?->slug) {
            'admin', 'sub-admin' => route('admin.dashboard'),
            'instructor' => route('instructor.dashboard'),
            default => route('learner.dashboard'),
        };
    }
}
