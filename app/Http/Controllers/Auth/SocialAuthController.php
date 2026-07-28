<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\AuthSecurityService;
use App\Services\IntegrationService;
use App\Services\LoginGreetingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends Controller
{
    public function __construct(
        protected IntegrationService $integrations,
        protected AuthSecurityService $security,
    ) {}

    public function redirect(Request $request, string $provider)
    {
        $provider = $this->normalize($provider);
        $this->integrations->applySocialiteConfig();
        $driver = $this->driverName($provider);

        if (! config("services.{$driver}.client_id") && ! config("services.{$provider}.client_id")) {
            return redirect()->route('login')->withErrors([
                'email' => ucfirst($provider).' login is not configured. Set credentials in Integrations.',
            ]);
        }

        session(['social_oauth_intent' => $request->query('intent', 'login')]);

        try {
            return Socialite::driver($driver)->scopes($this->scopes($provider))->redirect();
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('login')->withErrors([
                'email' => ucfirst($provider).' login is unavailable: '.$e->getMessage(),
            ]);
        }
    }

    public function callback(Request $request, string $provider)
    {
        $provider = $this->normalize($provider);
        $this->integrations->applySocialiteConfig();
        $driver = $this->driverName($provider);

        try {
            $socialUser = Socialite::driver($driver)->user();
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('login')->withErrors([
                'email' => ucfirst($provider).' sign-in failed. Please try again.',
            ]);
        }

        $email = strtolower(trim((string) $socialUser->getEmail()));
        $socialId = (string) $socialUser->getId();
        $name = trim((string) ($socialUser->getName() ?: $socialUser->getNickname() ?: 'User'));
        $column = $provider.'_id';

        if ($socialId === '') {
            return redirect()->route('login')->withErrors(['email' => 'Provider did not return a user id.']);
        }

        $user = User::query()
            ->where($column, $socialId)
            ->when($email !== '', fn ($q) => $q->orWhere('email', $email))
            ->first();

        if (! $user) {
            if ($email === '') {
                return redirect()->route('login')->withErrors([
                    'email' => 'Provider did not return an email. Link an existing account first.',
                ]);
            }

            $learnerRole = Role::where('slug', 'learner')->first();
            $user = User::create([
                'name' => $name ?: 'Learner',
                'email' => $email,
                'password' => bcrypt(Str::random(32)),
                'role_id' => $learnerRole?->id,
                'is_active' => true,
                'email_verified_at' => now(),
                $column => $socialId,
            ]);
        } else {
            $updates = [$column => $socialId];
            if (! $user->email_verified_at && $email) {
                $updates['email_verified_at'] = now();
            }
            $user->forceFill($updates)->save();
        }

        if (! $user->is_active) {
            return redirect()->route('login')->withErrors(['email' => 'Your account has been deactivated.']);
        }

        Auth::login($user, true);
        $user->update(['last_login_at' => now()]);
        ActivityLogger::log('login_'.$provider, "User {$user->name} logged in via {$provider}", $user, [
            'provider' => $provider,
        ]);
        $request->session()->regenerate();
        $cookie = $this->security->afterSuccessfulLogin($user, $request, $provider);

        if (in_array($user->role?->slug, ['admin', 'sub-admin'], true)) {
            LoginGreetingService::flashForUser($user);
        }

        $redirect = $user->isStudentPanelUser()
            ? route('learner.dashboard')
            : ($user->isSuperAdmin() ? route('platform.dashboard') : route('admin.dashboard'));

        return redirect()->intended($redirect)->cookie($cookie);
    }

    protected function normalize(string $provider): string
    {
        $provider = strtolower($provider);
        abort_unless(in_array($provider, ['facebook', 'apple', 'linkedin'], true), 404);

        return $provider;
    }

    protected function driverName(string $provider): string
    {
        return match ($provider) {
            'linkedin' => 'linkedin-openid',
            default => $provider,
        };
    }

    protected function scopes(string $provider): array
    {
        return match ($provider) {
            'facebook' => ['email'],
            'linkedin' => ['openid', 'profile', 'email'],
            default => [],
        };
    }
}
