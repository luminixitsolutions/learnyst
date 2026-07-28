<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\AuthSecurityService;
use App\Services\LoginGreetingService;
use App\Services\OtpService;
use App\Services\TotpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TwoFactorController extends Controller
{
    public function __construct(
        protected TotpService $totp,
        protected OtpService $otp,
        protected AuthSecurityService $security,
    ) {}

    public function showChallenge(Request $request)
    {
        if (! session('2fa:user:id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    public function verifyChallenge(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string'],
            'method' => ['nullable', 'in:totp,email'],
        ]);

        $userId = session('2fa:user:id');
        $user = User::find($userId);
        if (! $user) {
            return redirect()->route('login');
        }

        $method = $request->input('method', 'totp');
        $ok = false;

        if ($method === 'email') {
            try {
                $this->otp->verify($user->email, '2fa', $request->code, $user);
                $ok = true;
            } catch (ValidationException $e) {
                throw $e;
            }
        } else {
            if ($user->two_factor_secret) {
                $secret = $this->totp->decryptSecret($user->two_factor_secret);
                $ok = $this->totp->verify($secret, $request->code);
            }
            if (! $ok && $user->two_factor_recovery_codes) {
                $codes = json_decode($user->two_factor_recovery_codes, true) ?: [];
                $normalized = strtoupper(trim($request->code));
                if (in_array($normalized, $codes, true)) {
                    $ok = true;
                    $codes = array_values(array_diff($codes, [$normalized]));
                    $user->forceFill(['two_factor_recovery_codes' => json_encode($codes)])->save();
                }
            }
        }

        if (! $ok) {
            $this->security->recordLogin($user, $request, 'failed', '2fa');
            ActivityLogger::logAs($user, '2fa_failed', "Invalid 2FA code for {$user->email}", $user, [
                'method' => $method,
            ]);
            throw ValidationException::withMessages(['code' => 'Invalid authentication code.']);
        }

        session()->forget(['2fa:user:id', '2fa:remember', '2fa:panel']);
        Auth::login($user, (bool) session('2fa:remember'));
        $user->update(['last_login_at' => now()]);
        ActivityLogger::log('login', "User {$user->name} completed 2FA login", $user, [
            'provider' => '2fa',
            'method' => $method,
        ]);
        $request->session()->regenerate();
        $cookie = $this->security->afterSuccessfulLogin($user, $request, '2fa');

        if (in_array($user->role?->slug, ['admin', 'sub-admin', 'counselor'], true)) {
            LoginGreetingService::flashForUser($user);
        }

        $redirect = match ($user->role?->slug) {
            'super-admin' => route('platform.dashboard'),
            'admin', 'sub-admin', 'counselor' => route('admin.dashboard'),
            'instructor' => route('instructor.dashboard'),
            'learner', 'alumni', 'parent' => route('learner.dashboard'),
            default => route('home'),
        };

        return redirect()->intended($redirect)->cookie($cookie);
    }

    public function sendEmailOtp(Request $request)
    {
        $userId = session('2fa:user:id');
        $user = User::find($userId);
        if (! $user) {
            return redirect()->route('login');
        }

        $code = $this->otp->issue($user, 'email', $user->email, '2fa');
        if (app()->environment('local')) {
            session()->flash('otp_debug', $code);
        }

        return back()->with('success', 'A verification code was sent to your email.');
    }
}
