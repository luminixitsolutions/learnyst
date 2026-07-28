<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\IpAccessRule;
use App\Models\LoginDevice;
use App\Models\LoginHistory;
use App\Models\Setting;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\AuthSecurityService;
use App\Services\OtpService;
use App\Services\TotpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SecurityController extends Controller
{
    use ScopesToCurrentUser;

    public function __construct(
        protected TotpService $totp,
        protected OtpService $otp,
        protected AuthSecurityService $security,
    ) {}

    public function index()
    {
        $user = Auth::user();
        $devices = LoginDevice::where('user_id', $user->id)->latest('last_seen_at')->limit(20)->get();
        $history = LoginHistory::where('user_id', $user->id)->latest()->limit(30)->get();
        $ipRules = $this->owned(IpAccessRule::query())->latest()->get();
        $settings = [
            'max_parallel_devices' => Setting::get('max_parallel_devices', '3', 'security'),
            'require_2fa_admins' => Setting::get('require_2fa_admins', '0', 'security'),
        ];

        return view('admin.security.index', compact('user', 'devices', 'history', 'ipRules', 'settings'));
    }

    public function enable2faSetup()
    {
        $user = Auth::user();
        $secret = $this->totp->generateSecret();
        session(['2fa_setup_secret' => $secret]);
        $uri = $this->totp->provisioningUri($user, $secret);

        return view('admin.security.two-factor-setup', compact('secret', 'uri', 'user'));
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

        return redirect()->route('admin.security.index')
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

    public function revokeDevice(LoginDevice $device)
    {
        abort_unless($device->user_id === Auth::id() || $this->ownsUser($device->user_id), 403);
        $device->update(['revoked_at' => now()]);
        ActivityLogger::log('device_revoked', 'Device session revoked', $device);

        return back()->with('success', 'Device revoked.');
    }

    public function storeIpRule(Request $request)
    {
        $validated = $request->validate([
            'rule_type' => ['required', 'in:allow,deny'],
            'ip_cidr' => ['required', 'string', 'max:64'],
            'label' => ['nullable', 'string', 'max:120'],
        ]);

        $validated['created_by'] = Auth::id();
        $validated['scope'] = 'company';
        $validated['is_active'] = true;
        IpAccessRule::create($validated);

        return back()->with('success', 'IP rule added.');
    }

    public function destroyIpRule(IpAccessRule $ipRule)
    {
        $this->authorizeOwner($ipRule);
        $ipRule->delete();

        return back()->with('success', 'IP rule removed.');
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'max_parallel_devices' => ['required', 'integer', 'min:1', 'max:20'],
        ]);
        Setting::set('max_parallel_devices', (string) $validated['max_parallel_devices'], 'security');
        Setting::set('require_2fa_admins', $request->boolean('require_2fa_admins') ? '1' : '0', 'security');

        return back()->with('success', 'Security settings saved.');
    }

    public function loginHistory(Request $request)
    {
        $learnerIds = $this->ownedUsersQuery('learner')->pluck('id');
        $staffIds = collect([Auth::id()])->merge(
            $this->ownedUsersQuery()->pluck('id')
        )->unique();

        $history = LoginHistory::query()
            ->whereIn('user_id', $staffIds->merge($learnerIds))
            ->latest()
            ->paginate(40);

        return view('admin.security.login-history', compact('history'));
    }

    protected function ownsUser(int $userId): bool
    {
        return User::where('id', $userId)->where('created_by', Auth::id())->exists()
            || Auth::id() === $userId;
    }
}
