<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\Company;
use App\Services\SignupFormService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SignupController extends Controller
{
    protected array $steps = [
        'account',
        'company',
        'business_type',
        'teach',
        'goal',
        'content_ready',
        'audience',
        'source',
        'verify',
    ];

    public function show(Request $request, ?string $step = null)
    {
        $step = $step ?: 'account';
        abort_unless(in_array($step, $this->steps, true), 404);

        $data = session('signup', []);

        if ($step === 'verify') {
            abort_unless(session('signup_verify_email'), 404);
        } elseif ($step !== 'account' && empty($data['email'])) {
            return redirect()->route('signup.show', 'account');
        } elseif ($step !== 'account' && $step !== 'company' && empty($data['company_name'])) {
            return redirect()->route('signup.show', 'company');
        }

        // Skip teach unless selected business type opens that step
        if ($step === 'teach' && ! SignupFormService::opensTeach($data['business_type'] ?? null)) {
            return redirect()->route('signup.show', 'goal');
        }

        return view($this->viewForStep($step), [
            'step' => $step,
            'steps' => $this->visibleSteps($data),
            'stepIndex' => $this->stepIndex($step, $data),
            'data' => $data,
            'options' => $this->options(),
            'question' => $this->questionMeta($step, $data),
        ]);
    }

    protected function viewForStep(string $step): string
    {
        return match ($step) {
            'account' => 'auth.signup.account',
            'company' => 'auth.signup.company',
            'verify' => 'auth.signup.verify',
            default => 'auth.signup.question',
        };
    }

    protected function questionMeta(string $step, array $data): array
    {
        $form = in_array($step, array_keys(SignupFormService::questions()), true)
            ? SignupFormService::get($step)
            : [];

        $actions = [
            'business_type' => route('signup.business_type'),
            'teach' => route('signup.teach'),
            'goal' => route('signup.goal'),
            'content_ready' => route('signup.content_ready'),
            'audience' => route('signup.audience'),
            'source' => route('signup.source'),
        ];

        $backs = [
            'business_type' => 'company',
            'teach' => 'business_type',
            'goal' => SignupFormService::opensTeach($data['business_type'] ?? null) ? 'teach' : 'business_type',
            'content_ready' => 'goal',
            'audience' => 'content_ready',
            'source' => 'audience',
        ];

        if (! isset($actions[$step])) {
            return [];
        }

        return [
            'title' => $form['title'] ?? '',
            'subtitle' => $form['subtitle'] ?? null,
            'label' => $form['label'] ?? null,
            'field' => $step,
            'option_key' => $step,
            'action' => $actions[$step],
            'back' => $backs[$step],
            'button' => $step === 'source' ? 'Submit →' : 'Next →',
            'two_col' => $step === 'source',
        ];
    }

    public function storeAccount(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::min(6)],
            'terms' => ['accepted'],
        ], [
            'terms.accepted' => 'Please agree to the Terms and Privacy Policy.',
        ]);

        $signup = session('signup', []);
        $signup['email'] = $validated['email'];
        $signup['password'] = $validated['password'];
        session(['signup' => $signup]);

        return redirect()->route('signup.show', 'company');
    }

    public function storeCompany(Request $request)
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
        ]);

        $signup = session('signup', []);
        abort_unless(! empty($signup['email']), 403);

        $signup['company_name'] = $validated['company_name'];
        $signup['phone'] = $validated['phone'];
        session(['signup' => $signup]);

        return redirect()->route('signup.show', 'business_type');
    }

    public function storeBusinessType(Request $request)
    {
        $validated = $request->validate([
            'business_type' => ['required', 'string', 'in:'.implode(',', array_keys($this->options()['business_type']))],
        ]);

        $signup = session('signup', []);
        $signup['business_type'] = $validated['business_type'];
        unset($signup['teach']);
        session(['signup' => $signup]);

        if ($validated['business_type'] && SignupFormService::opensTeach($validated['business_type'])) {
            return redirect()->route('signup.show', 'teach');
        }

        return redirect()->route('signup.show', 'goal');
    }

    public function storeTeach(Request $request)
    {
        $validated = $request->validate([
            'teach' => ['required', 'string', 'in:'.implode(',', array_keys($this->options()['teach']))],
        ]);

        $signup = session('signup', []);
        $signup['teach'] = $validated['teach'];
        session(['signup' => $signup]);

        return redirect()->route('signup.show', 'goal');
    }

    public function storeGoal(Request $request)
    {
        $validated = $request->validate([
            'goal' => ['required', 'string', 'in:'.implode(',', array_keys($this->options()['goal']))],
        ]);

        $signup = session('signup', []);
        $signup['goal'] = $validated['goal'];
        session(['signup' => $signup]);

        return redirect()->route('signup.show', 'content_ready');
    }

    public function storeContentReady(Request $request)
    {
        $validated = $request->validate([
            'content_ready' => ['required', 'string', 'in:'.implode(',', array_keys($this->options()['content_ready']))],
        ]);

        $signup = session('signup', []);
        $signup['content_ready'] = $validated['content_ready'];
        session(['signup' => $signup]);

        return redirect()->route('signup.show', 'audience');
    }

    public function storeAudience(Request $request)
    {
        $validated = $request->validate([
            'audience' => ['required', 'string', 'in:'.implode(',', array_keys($this->options()['audience']))],
        ]);

        $signup = session('signup', []);
        $signup['audience'] = $validated['audience'];
        session(['signup' => $signup]);

        return redirect()->route('signup.show', 'source');
    }

    public function storeSource(Request $request)
    {
        $validated = $request->validate([
            'source' => ['required', 'string', 'in:'.implode(',', array_keys($this->options()['source']))],
        ]);

        $signup = session('signup', []);
        abort_unless(! empty($signup['email']) && ! empty($signup['password']) && ! empty($signup['company_name']), 403);

        $signup['source'] = $validated['source'];

        $adminRole = Role::where('slug', 'admin')->firstOrFail();

        $user = User::create([
            'role_id' => $adminRole->id,
            'name' => $signup['company_name'],
            'email' => $signup['email'],
            'phone' => $signup['phone'] ?? null,
            'password' => Hash::make($signup['password']),
            'notes' => json_encode([
                'onboarding' => collect($signup)->only([
                    'business_type', 'teach', 'goal', 'content_ready', 'audience', 'source',
                ])->all(),
            ]),
            'is_active' => true,
            'email_verified_at' => null,
        ]);

        Company::firstOrCreateForOwner($user);

        session()->forget('signup');
        session(['signup_verify_email' => $user->email]);

        return redirect()->route('signup.show', 'verify');
    }

    public function resendVerification(Request $request)
    {
        $email = session('signup_verify_email');
        if (! $email) {
            return redirect()->route('signup.show', 'account');
        }

        return back()->with('success', 'Verification email resent. Please check your inbox/spam.');
    }

    public function markVerifiedAndLogin(Request $request)
    {
        $email = session('signup_verify_email');
        abort_unless($email, 404);

        $user = User::where('email', $email)->firstOrFail();
        $user->forceFill(['email_verified_at' => now()])->save();

        session()->forget('signup_verify_email');
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    protected function options(): array
    {
        return SignupFormService::allChoices();
    }

    protected function visibleSteps(array $data): array
    {
        $steps = ['account', 'company', 'business_type'];
        if (SignupFormService::opensTeach($data['business_type'] ?? null)) {
            $steps[] = 'teach';
        }
        $steps = array_merge($steps, ['goal', 'content_ready', 'audience', 'source', 'verify']);

        return $steps;
    }

    protected function stepIndex(string $step, array $data): int
    {
        $steps = $this->visibleSteps($data);
        $index = array_search($step, $steps, true);

        return $index === false ? 0 : $index;
    }
}
