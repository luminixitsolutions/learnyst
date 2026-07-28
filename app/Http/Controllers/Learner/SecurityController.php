<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\LoginDevice;
use App\Models\LoginHistory;
use App\Services\AuthSecurityService;
use App\Services\OtpService;
use App\Services\TotpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SecurityController extends Controller
{
    public function __construct(
        protected AuthSecurityService $security,
        protected TotpService $totp,
        protected OtpService $otp,
    ) {}

    public function index()
    {
        $user = Auth::user();
        $devices = LoginDevice::where('user_id', $user->id)->latest('last_seen_at')->limit(20)->get();
        $history = LoginHistory::where('user_id', $user->id)->latest()->limit(20)->get();

        return view('learner.security.index', compact('user', 'devices', 'history'));
    }

    public function revokeDevice(LoginDevice $device)
    {
        abort_unless($device->user_id === Auth::id(), 403);
        $device->update(['revoked_at' => now()]);

        return back()->with('success', 'Device revoked.');
    }

    public function setup2fa()
    {
        $user = Auth::user();
        $secret = $this->totp->generateSecret();
        session(['2fa_setup_secret' => $secret]);
        $uri = $this->totp->provisioningUri($user, $secret);

        return view('learner.security.two-factor-setup', compact('secret', 'uri', 'user'));
    }

    public function confirm2fa(Request $request)
    {
        $request->validate(['code' => ['required', 'string']]);
        $secret = session('2fa_setup_secret');
        if (! $secret || ! $this->totp->verify($secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid authenticator code.']);
        }

        $codes = $this->totp->recoveryCodes();
        Auth::user()->forceFill([
            'two_factor_secret' => $this->totp->encryptSecret($secret),
            'two_factor_enabled' => true,
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => json_encode($codes),
        ])->save();
        session()->forget('2fa_setup_secret');

        return redirect()->route('learner.security.index')
            ->with('success', 'Two-factor authentication enabled.')
            ->with('recovery_codes', $codes);
    }

    public function disable2fa(Request $request)
    {
        $request->validate(['password' => ['required', 'current_password']]);
        Auth::user()->forceFill([
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
            'two_factor_confirmed_at' => null,
            'two_factor_recovery_codes' => null,
        ])->save();

        return back()->with('success', 'Two-factor authentication disabled.');
    }
}
