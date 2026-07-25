<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\GoogleOAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function __construct(protected GoogleOAuthService $google)
    {
    }

    public function redirect(Request $request)
    {
        if (! $this->google->isEnabled()) {
            return $this->disabledRedirect($request);
        }

        $intent = $request->query('intent', 'login');
        if (! in_array($intent, ['login', 'signup'], true)) {
            $intent = 'login';
        }

        session([
            'google_oauth_intent' => $intent,
            'google_oauth_return' => $intent === 'signup' ? 'signup' : 'login',
        ]);

        $this->google->configure();

        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    public function callback(Request $request)
    {
        if (! $this->google->isEnabled()) {
            return $this->disabledRedirect($request);
        }

        $intent = session('google_oauth_intent', 'login');

        try {
            $this->google->configure();
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            report($e);

            return redirect()
                ->route($intent === 'signup' ? 'signup.show' : 'login')
                ->withErrors(['email' => 'Google sign-in failed. Please try again.']);
        }

        $email = strtolower(trim((string) $googleUser->getEmail()));
        $googleId = (string) $googleUser->getId();
        $name = trim((string) ($googleUser->getName() ?: $googleUser->getNickname() ?: 'Institute'));

        if ($email === '' || $googleId === '') {
            return redirect()
                ->route($intent === 'signup' ? 'signup.show' : 'login')
                ->withErrors(['email' => 'Google did not return a valid email address.']);
        }

        $user = User::query()
            ->where(function ($query) use ($googleId, $email) {
                $query->where('google_id', $googleId)
                    ->orWhere('email', $email);
            })
            ->first();

        if ($intent === 'signup') {
            return $this->handleSignup($request, $user, $email, $googleId, $name);
        }

        return $this->handleLogin($request, $user, $email, $googleId, $name);
    }

    protected function handleSignup(Request $request, ?User $user, string $email, string $googleId, string $name)
    {
        if ($user) {
            // Existing account — link Google if needed and log them in (institute roles only).
            if (! in_array($user->role?->slug, ['admin', 'sub-admin'], true)) {
                return redirect()
                    ->route('login')
                    ->withErrors(['email' => 'This Google account cannot start an institute signup. Use institute login or a different Google account.']);
            }

            $this->linkGoogle($user, $googleId);
            session()->forget(['google_oauth_intent', 'google_oauth_return', 'signup']);

            return $this->loginUser($request, $user, 'Google signup matched an existing institute account.');
        }

        if (User::where('email', $email)->exists()) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'An account with this email already exists. Please sign in.']);
        }

        $signup = session('signup', []);
        $signup['email'] = $email;
        $signup['google_id'] = $googleId;
        $signup['google_name'] = $name;
        $signup['auth_provider'] = 'google';
        unset($signup['password']);
        session(['signup' => $signup]);
        session()->forget(['google_oauth_intent', 'google_oauth_return']);

        return redirect()
            ->route('signup.show', 'company')
            ->with('success', 'Google account connected. Continue setting up your institute.');
    }

    protected function handleLogin(Request $request, ?User $user, string $email, string $googleId, string $name)
    {
        if (! $user) {
            // No account yet — start signup with Google identity.
            $signup = session('signup', []);
            $signup['email'] = $email;
            $signup['google_id'] = $googleId;
            $signup['google_name'] = $name;
            $signup['auth_provider'] = 'google';
            unset($signup['password']);
            session(['signup' => $signup]);
            session()->forget(['google_oauth_intent', 'google_oauth_return']);

            return redirect()
                ->route('signup.show', 'company')
                ->with('success', 'No institute account found. Continue with Google signup.');
        }

        if (! $user->is_active) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Your account has been deactivated.']);
        }

        if (! in_array($user->role?->slug, ['admin', 'sub-admin'], true)) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'This Google account cannot access the Institute Panel.']);
        }

        $this->linkGoogle($user, $googleId);
        session()->forget(['google_oauth_intent', 'google_oauth_return']);

        return $this->loginUser($request, $user, "User {$user->name} logged in via Google");
    }

    protected function linkGoogle(User $user, string $googleId): void
    {
        $updates = [];
        if ($user->google_id !== $googleId) {
            $updates['google_id'] = $googleId;
        }
        if (! $user->email_verified_at) {
            $updates['email_verified_at'] = now();
        }
        if ($updates) {
            $user->forceFill($updates)->save();
        }
    }

    protected function loginUser(Request $request, User $user, string $logMessage)
    {
        Auth::login($user, true);
        $user->update(['last_login_at' => now()]);
        ActivityLogger::log('login', $logMessage, $user);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    protected function disabledRedirect(Request $request)
    {
        $intent = $request->query('intent', session('google_oauth_intent', 'login'));

        return redirect()
            ->route($intent === 'signup' ? 'signup.show' : 'login')
            ->withErrors(['email' => 'Google sign-in is not configured. Ask the platform admin to add Google OAuth keys.']);
    }
}
