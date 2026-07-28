<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\ReferralCode;
use App\Models\Setting;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReferralController extends Controller
{
    use ScopesToCurrentUser;

    public function __construct(protected ReferralService $referrals) {}

    public function index(Request $request)
    {
        $codes = $this->owned(ReferralCode::query())
            ->with(['user'])
            ->withCount('referrals')
            ->latest()
            ->get();

        $query = $this->owned(Referral::query())
            ->with(['referrer', 'referred', 'referralCode'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('referrer', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('referred', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('referralCode', fn ($c) => $c->where('code', 'like', "%{$search}%"));
            });
        }

        $referrals = $query->limit(500)->get();

        $stats = [
            'codes' => $this->owned(ReferralCode::query())->count(),
            'total' => $this->owned(Referral::query())->count(),
            'rewarded' => $this->owned(Referral::query())->where('status', 'rewarded')->count(),
            'pending' => $this->owned(Referral::query())->where('status', 'pending')->count(),
        ];

        $learners = $this->visibleLearnersQuery()->orderBy('name')->get();

        return view('admin.referrals.index', compact('codes', 'referrals', 'stats', 'learners'));
    }

    public function storeCode(Request $request)
    {
        $ownedLearnerIds = $this->visibleLearnersQuery()->pluck('id');

        $validated = $request->validate([
            'user_id' => ['required', Rule::in($ownedLearnerIds)],
            'code' => ['nullable', 'string', 'max:40', 'unique:referral_codes,code'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
        ]);

        $learner = User::findOrFail($validated['user_id']);
        $code = $this->referrals->ensureCode($learner, Auth::id());

        if (! empty($validated['code']) && $code->uses_count === 0) {
            $code->update(['code' => strtoupper($validated['code'])]);
        }
        if (array_key_exists('max_uses', $validated)) {
            $code->update(['max_uses' => $validated['max_uses']]);
        }

        ActivityLogger::log('referral_code_created', "Referral code {$code->code} for {$learner->name}", $code);

        return back()->with('success', "Referral code {$code->code} ready.");
    }

    public function apply(Request $request)
    {
        $ownedLearnerIds = $this->visibleLearnersQuery()->pluck('id');

        $validated = $request->validate([
            'referred_id' => ['required', Rule::in($ownedLearnerIds)],
            'referral_code' => ['required', 'string', 'max:40'],
        ]);

        $referred = User::findOrFail($validated['referred_id']);
        $this->referrals->applyCode($referred, $validated['referral_code'], Auth::id());

        return back()->with('success', 'Referral applied and rewards processed where applicable.');
    }

    public function reward(Referral $referral)
    {
        $this->authorizeOwner($referral);
        $this->referrals->qualifyAndReward($referral);

        return back()->with('success', 'Referral rewarded.');
    }

    public function toggleCode(ReferralCode $referralCode)
    {
        $this->authorizeOwner($referralCode);
        $referralCode->update(['is_active' => ! $referralCode->is_active]);

        return back()->with('success', 'Referral code '.($referralCode->is_active ? 'activated' : 'deactivated').'.');
    }

    public function settings()
    {
        $settings = Setting::where('group', 'referral')->get()->pluck('value', 'key');

        return view('admin.referrals.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'enabled' => ['nullable', 'boolean'],
            'reward_type' => ['required', 'in:wallet,coupon,free_days'],
            'reward_on' => ['required', 'in:signup,first_purchase'],
            'referrer_reward' => ['required', 'numeric', 'min:0'],
            'referred_reward' => ['required', 'numeric', 'min:0'],
            'free_days' => ['nullable', 'integer', 'min:0'],
        ]);

        Setting::set('enabled', $request->boolean('enabled') ? '1' : '0', 'referral');
        Setting::set('reward_type', $validated['reward_type'], 'referral');
        Setting::set('reward_on', $validated['reward_on'], 'referral');
        Setting::set('referrer_reward', (string) $validated['referrer_reward'], 'referral');
        Setting::set('referred_reward', (string) $validated['referred_reward'], 'referral');
        Setting::set('free_days', (string) ($validated['free_days'] ?? 0), 'referral');

        ActivityLogger::log('referral_settings_updated', 'Referral settings updated');

        return back()->with('success', 'Referral settings saved.');
    }
}
