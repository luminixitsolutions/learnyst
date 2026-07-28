<?php

namespace App\Services;

use App\Models\IpAccessRule;
use App\Models\LoginDevice;
use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthSecurityService
{
    public function deviceId(Request $request): string
    {
        $cookie = $request->cookie('sn_device_id');
        if ($cookie && strlen($cookie) >= 16) {
            return $cookie;
        }

        return hash('sha256', ($request->userAgent() ?: 'ua').'|'.($request->ip() ?: 'ip').'|'.Str::random(8));
    }

    public function recordLogin(?User $user, Request $request, string $status, ?string $provider = null, ?string $email = null): void
    {
        LoginHistory::create([
            'user_id' => $user?->id,
            'email' => $email ?: $user?->email,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
            'device_id' => $this->deviceId($request),
            'status' => $status,
            'provider' => $provider,
        ]);
    }

    public function registerDevice(User $user, Request $request): LoginDevice
    {
        $deviceId = $this->deviceId($request);

        return LoginDevice::updateOrCreate(
            ['user_id' => $user->id, 'device_id' => $deviceId],
            [
                'device_name' => $this->guessDeviceName($request->userAgent()),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
                'ip_address' => $request->ip(),
                'session_id' => $request->session()->getId(),
                'last_seen_at' => now(),
                'revoked_at' => null,
            ]
        );
    }

    public function enforceParallelLoginLimit(User $user, Request $request, int $maxDevices = 3): void
    {
        $active = LoginDevice::where('user_id', $user->id)
            ->whereNull('revoked_at')
            ->orderByDesc('last_seen_at')
            ->get();

        if ($active->count() <= $maxDevices) {
            return;
        }

        $keepIds = $active->take($maxDevices)->pluck('id')->all();
        LoginDevice::where('user_id', $user->id)
            ->whereNull('revoked_at')
            ->whereNotIn('id', $keepIds)
            ->update(['revoked_at' => now()]);
    }

    public function isDeviceRevoked(User $user, Request $request): bool
    {
        $device = LoginDevice::where('user_id', $user->id)
            ->where('device_id', $this->deviceId($request))
            ->first();

        return $device && $device->revoked_at;
    }

    public function ipAllowed(?int $companyUserId, string $ip, bool $platform = false): bool
    {
        $rules = IpAccessRule::query()
            ->where('is_active', true)
            ->when(
                $platform,
                fn ($q) => $q->where('scope', 'platform'),
                fn ($q) => $q->where('scope', 'company')->where('created_by', $companyUserId)
            )
            ->get();

        if ($rules->isEmpty()) {
            return true;
        }

        $denies = $rules->where('rule_type', 'deny');
        foreach ($denies as $rule) {
            if ($this->ipMatches($ip, $rule->ip_cidr)) {
                return false;
            }
        }

        $allows = $rules->where('rule_type', 'allow');
        if ($allows->isEmpty()) {
            return true;
        }

        foreach ($allows as $rule) {
            if ($this->ipMatches($ip, $rule->ip_cidr)) {
                return true;
            }
        }

        return false;
    }

    public function ipMatches(string $ip, string $cidr): bool
    {
        if (! str_contains($cidr, '/')) {
            return $ip === $cidr;
        }
        [$subnet, $mask] = explode('/', $cidr, 2);
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) {
            return $ip === $subnet;
        }
        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $mask = (int) $mask;
        $maskLong = -1 << (32 - $mask);

        return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
    }

    protected function guessDeviceName(?string $ua): string
    {
        $ua = $ua ?: 'Unknown';
        if (str_contains($ua, 'Edg/')) {
            return 'Edge';
        }
        if (str_contains($ua, 'Chrome')) {
            return 'Chrome';
        }
        if (str_contains($ua, 'Firefox')) {
            return 'Firefox';
        }
        if (str_contains($ua, 'Safari')) {
            return 'Safari';
        }

        return 'Browser';
    }

    public function afterSuccessfulLogin(User $user, Request $request, ?string $provider = null): \Symfony\Component\HttpFoundation\Cookie
    {
        $this->recordLogin($user, $request, 'success', $provider);
        $this->registerDevice($user, $request);
        $this->enforceParallelLoginLimit($user, $request, (int) (\App\Models\Setting::get('max_parallel_devices', 3, 'security') ?: 3));
        app(GamificationService::class)->recordLogin($user);

        $deviceId = $this->deviceId($request);

        return cookie('sn_device_id', $deviceId, 60 * 24 * 365, null, null, false, true, false, 'Lax');
    }
}
