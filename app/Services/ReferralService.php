<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Referral;
use App\Models\ReferralCode;
use App\Models\Setting;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReferralService
{
    public function __construct(protected WalletService $wallets) {}

    public function isEnabled(): bool
    {
        return filter_var(Setting::get('enabled', '1', 'referral'), FILTER_VALIDATE_BOOLEAN);
    }

    public function referrerReward(): float
    {
        return (float) Setting::get('referrer_reward', '100', 'referral');
    }

    public function referredReward(): float
    {
        return (float) Setting::get('referred_reward', '50', 'referral');
    }

    public function rewardType(): string
    {
        return Setting::get('reward_type', 'wallet', 'referral') ?: 'wallet';
    }

    public function rewardOn(): string
    {
        return Setting::get('reward_on', 'signup', 'referral') ?: 'signup';
    }

    public function ensureCode(User $learner, ?int $createdBy = null): ReferralCode
    {
        $createdBy = $createdBy ?? $learner->created_by;

        return ReferralCode::firstOrCreate(
            [
                'user_id' => $learner->id,
                'created_by' => $createdBy,
            ],
            [
                'code' => ReferralCode::generateUniqueCode($learner),
                'is_active' => true,
                'uses_count' => 0,
            ]
        );
    }

    public function applyCode(User $referred, string $code, ?int $createdBy = null): Referral
    {
        if (! $this->isEnabled()) {
            throw ValidationException::withMessages(['referral_code' => 'Referral program is disabled.']);
        }

        $code = strtoupper(trim($code));
        $referralCode = ReferralCode::where('code', $code)->first();

        if (! $referralCode || ! $referralCode->canBeUsed()) {
            throw ValidationException::withMessages(['referral_code' => 'Invalid or inactive referral code.']);
        }

        if ((int) $referralCode->user_id === (int) $referred->id) {
            throw ValidationException::withMessages(['referral_code' => 'You cannot use your own referral code.']);
        }

        $createdBy = $createdBy ?? $referred->created_by ?? $referralCode->created_by;

        $existing = Referral::where('referred_id', $referred->id)
            ->when($createdBy, fn ($q) => $q->where('created_by', $createdBy))
            ->first();

        if ($existing) {
            throw ValidationException::withMessages(['referral_code' => 'This learner already has a referral attributed.']);
        }

        return DB::transaction(function () use ($referralCode, $referred, $createdBy) {
            $referral = Referral::create([
                'referral_code_id' => $referralCode->id,
                'referrer_id' => $referralCode->user_id,
                'referred_id' => $referred->id,
                'created_by' => $createdBy,
                'status' => 'pending',
                'reward_type' => $this->rewardType(),
                'referrer_reward' => $this->referrerReward(),
                'referred_reward' => $this->referredReward(),
            ]);

            $referralCode->increment('uses_count');

            if ($this->rewardOn() === 'signup') {
                $this->qualifyAndReward($referral);
            }

            ActivityLogger::log('referral_created', "Referral applied via {$referralCode->code}", $referral);

            return $referral->fresh(['referrer', 'referred', 'referralCode']);
        });
    }

    public function markQualifiedFromOrder(Order $order): ?Referral
    {
        if (! $this->isEnabled() || $this->rewardOn() !== 'first_purchase') {
            return null;
        }

        $referral = Referral::where('referred_id', $order->user_id)
            ->whereIn('status', ['pending', 'qualified'])
            ->latest()
            ->first();

        if (! $referral || $referral->status === 'rewarded') {
            return null;
        }

        $referral->update([
            'order_id' => $order->id,
            'qualified_at' => now(),
            'status' => 'qualified',
        ]);

        return $this->qualifyAndReward($referral->fresh());
    }

    public function qualifyAndReward(Referral $referral): Referral
    {
        if ($referral->status === 'rewarded') {
            return $referral;
        }

        return DB::transaction(function () use ($referral) {
            $referral->update([
                'status' => 'qualified',
                'qualified_at' => $referral->qualified_at ?? now(),
            ]);

            if ($referral->reward_type === 'wallet') {
                $referrer = $referral->referrer;
                $referred = $referral->referred;

                if ($referrer && (float) $referral->referrer_reward > 0) {
                    $wallet = $this->wallets->getOrCreateForLearner($referrer, $referral->created_by);
                    $this->wallets->credit(
                        $wallet,
                        (float) $referral->referrer_reward,
                        WalletTransaction::SOURCE_REFERRAL_BONUS,
                        'Referral reward for inviting '.$referred?->name,
                        null,
                        $referral,
                        $referral->referralCode?->code,
                        ['referred_user' => $referred?->name]
                    );
                }

                if ($referred && (float) $referral->referred_reward > 0) {
                    $wallet = $this->wallets->getOrCreateForLearner($referred, $referral->created_by);
                    $this->wallets->credit(
                        $wallet,
                        (float) $referral->referred_reward,
                        WalletTransaction::SOURCE_REFERRAL_BONUS,
                        'Welcome reward via referral from '.$referrer?->name,
                        null,
                        $referral,
                        $referral->referralCode?->code,
                        ['referred_user' => $referred->name]
                    );
                }
            }

            $referral->update([
                'status' => 'rewarded',
                'rewarded_at' => now(),
            ]);

            ActivityLogger::log('referral_rewarded', 'Referral rewards issued', $referral);

            return $referral->fresh();
        });
    }
}
