<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\AuthSecurityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class StudentAuthController extends Controller
{
    public function __construct(protected AuthSecurityService $security) {}

    public function showLoginForm(Request $request)
    {
        if (Auth::check()) {
            return redirect($this->resolveRedirect($request) ?? $this->dashboardFor(Auth::user()));
        }

        $this->rememberRedirect($request);

        return view('auth.student-login', [
            'redirect' => $this->safeRedirect($request->query('redirect') ?: $request->old('redirect')),
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'redirect' => ['nullable', 'string', 'max:500'],
        ]);

        if (! Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $this->security->recordLogin(null, $request, 'failed', 'password', $credentials['email']);
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

        if (! $user->isStudentPanelUser()) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => __('This account is not a student portal account. Use institute login or platform admin login instead.'),
            ]);
        }

        if ($user->two_factor_enabled) {
            Auth::logout();
            $request->session()->put([
                '2fa:user:id' => $user->id,
                '2fa:remember' => $request->boolean('remember'),
                '2fa:panel' => 'learner',
                '2fa:redirect' => $this->resolveRedirect($request),
            ]);

            return redirect()->route('auth.2fa.challenge');
        }

        $user->update(['last_login_at' => now()]);
        ActivityLogger::log('login', "Student {$user->name} logged in", $user);
        $request->session()->regenerate();
        $cookie = $this->security->afterSuccessfulLogin($user, $request, 'password');

        return redirect()->to(
            $this->resolveRedirect($request) ?? $this->dashboardFor($user)
        )->cookie($cookie);
    }

    public function showRegisterForm(Request $request)
    {
        if (Auth::check()) {
            return redirect($this->resolveRedirect($request) ?? $this->dashboardFor(Auth::user()));
        }

        $this->rememberRedirect($request);

        return view('auth.student-register', [
            'redirect' => $this->safeRedirect($request->query('redirect') ?: $request->old('redirect')),
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:40'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'redirect' => ['nullable', 'string', 'max:500'],
        ]);

        $learnerRole = Role::query()->where('slug', 'learner')->firstOrFail();

        $user = User::create([
            'role_id' => $learnerRole->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        ActivityLogger::log('student_registered', "Student {$user->name} registered", $user);

        return redirect()
            ->to($this->resolveRedirect($request) ?? route('learner.dashboard'))
            ->with('success', 'Welcome! Your student account is ready.');
    }

    protected function rememberRedirect(Request $request): void
    {
        $redirect = $this->safeRedirect($request->query('redirect'));
        if ($redirect) {
            $request->session()->put('url.intended', url($redirect));
        }
    }

    protected function resolveRedirect(Request $request): ?string
    {
        $fromInput = $this->safeRedirect($request->input('redirect'));
        if ($fromInput) {
            return url($fromInput);
        }

        $intended = $request->session()->pull('url.intended');
        if (is_string($intended) && $intended !== '') {
            $path = parse_url($intended, PHP_URL_PATH);
            $safe = $this->safeRedirect($path);
            if ($safe) {
                return url($safe);
            }
        }

        return null;
    }

    /**
     * Only allow same-site relative paths (prevent open redirects).
     */
    protected function safeRedirect(?string $value): ?string
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        $value = trim($value);
        if (! str_starts_with($value, '/') || str_starts_with($value, '//') || str_contains($value, "\n")) {
            return null;
        }

        return $value;
    }

    protected function dashboardFor($user): string
    {
        return match ($user->role?->slug) {
            'super-admin' => route('platform.dashboard'),
            'admin', 'sub-admin', 'counselor' => route('admin.dashboard'),
            'instructor' => route('instructor.dashboard'),
            'alumni' => route('alumni.dashboard'),
            'parent' => route('parent.dashboard'),
            'learner' => route('learner.dashboard'),
            default => route('home'),
        };
    }
}
