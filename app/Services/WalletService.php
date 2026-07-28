<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WalletService
{
    public function isEnabled(): bool
    {
        return filter_var(Setting::get('enabled', '1', 'wallet'), FILTER_VALIDATE_BOOLEAN);
    }

    public function allowCheckoutRedeem(): bool
    {
        return filter_var(Setting::get('allow_checkout_redeem', '1', 'wallet'), FILTER_VALIDATE_BOOLEAN);
    }

    public function refundToWallet(): bool
    {
        return filter_var(Setting::get('refund_to_wallet', '1', 'wallet'), FILTER_VALIDATE_BOOLEAN);
    }

    public function signupBonusAmount(): float
    {
        return (float) Setting::get('signup_bonus', '0', 'wallet');
    }

    /**
     * Resolve or create the institute-scoped wallet for a learner.
     */
    public function getOrCreateForLearner(User $learner, ?int $createdBy = null): Wallet
    {
        $createdBy = $createdBy ?? $learner->created_by;

        return Wallet::firstOrCreate(
            [
                'user_id' => $learner->id,
                'created_by' => $createdBy,
            ],
            [
                'balance' => 0,
                'currency' => Setting::get('currency', 'INR', 'payment') ?: 'INR',
                'is_frozen' => false,
                'is_active' => true,
            ]
        );
    }

    public function credit(
        Wallet $wallet,
        float $amount,
        string $source = WalletTransaction::SOURCE_MANUAL,
        ?string $notes = null,
        ?User $performedBy = null,
        ?Model $reference = null,
        ?string $referralCode = null,
        array $meta = []
    ): WalletTransaction {
        return $this->apply($wallet, 'credit', $amount, $source, $notes, $performedBy, $reference, $referralCode, $meta);
    }

    public function debit(
        Wallet $wallet,
        float $amount,
        string $source = WalletTransaction::SOURCE_MANUAL,
        ?string $notes = null,
        ?User $performedBy = null,
        ?Model $reference = null,
        ?string $referralCode = null,
        array $meta = []
    ): WalletTransaction {
        return $this->apply($wallet, 'debit', $amount, $source, $notes, $performedBy, $reference, $referralCode, $meta);
    }

    /** Admin adjust: positive credits, negative debits. */
    public function adjust(Wallet $wallet, float $signedAmount, ?string $notes = null, ?User $performedBy = null): WalletTransaction
    {
        if ($signedAmount == 0.0) {
            throw ValidationException::withMessages(['amount' => 'Adjustment amount cannot be zero.']);
        }

        $type = $signedAmount > 0 ? 'credit' : 'debit';

        return $this->apply(
            $wallet,
            $type,
            abs($signedAmount),
            WalletTransaction::SOURCE_ADJUSTMENT,
            $notes,
            $performedBy
        );
    }

    public function topUp(Wallet $wallet, float $amount, ?string $notes = null, ?User $performedBy = null): WalletTransaction
    {
        return $this->credit($wallet, $amount, WalletTransaction::SOURCE_TOPUP, $notes, $performedBy);
    }

    public function spendOnOrder(Wallet $wallet, Order $order, float $amount, ?User $performedBy = null): WalletTransaction
    {
        if (! $this->allowCheckoutRedeem()) {
            throw ValidationException::withMessages(['wallet' => 'Wallet checkout is disabled.']);
        }

        return $this->debit(
            $wallet,
            $amount,
            WalletTransaction::SOURCE_ORDER_PAYMENT,
            'Payment for order '.$order->order_number,
            $performedBy,
            $order,
            null,
            ['order_number' => $order->order_number]
        );
    }

    public function creditRefund(Order $order, ?User $performedBy = null): ?WalletTransaction
    {
        if (! $this->refundToWallet()) {
            return null;
        }

        $learner = $order->user;
        if (! $learner) {
            return null;
        }

        $amount = (float) $order->wallet_amount > 0
            ? (float) $order->wallet_amount
            : (float) $order->total;

        if ($amount <= 0) {
            return null;
        }

        $createdBy = $learner->created_by;
        $wallet = $this->getOrCreateForLearner($learner, $createdBy ? (int) $createdBy : null);

        if ($wallet->is_frozen) {
            $wallet->update(['is_frozen' => false]);
        }

        return $this->credit(
            $wallet,
            $amount,
            WalletTransaction::SOURCE_REFUND,
            'Refund for order '.$order->order_number,
            $performedBy,
            $order,
            null,
            ['order_number' => $order->order_number]
        );
    }

    public function freeze(Wallet $wallet, bool $frozen = true, ?string $notes = null): Wallet
    {
        $wallet->update([
            'is_frozen' => $frozen,
            'notes' => $notes ?? $wallet->notes,
        ]);

        ActivityLogger::log(
            $frozen ? 'wallet_frozen' : 'wallet_unfrozen',
            'Wallet for '.$wallet->user?->name.' '.($frozen ? 'frozen' : 'unfrozen'),
            $wallet
        );

        return $wallet->fresh();
    }

    protected function apply(
        Wallet $wallet,
        string $type,
        float $amount,
        string $source,
        ?string $notes,
        ?User $performedBy,
        ?Model $reference = null,
        ?string $referralCode = null,
        array $meta = []
    ): WalletTransaction {
        if ($amount <= 0) {
            throw ValidationException::withMessages(['amount' => 'Amount must be greater than zero.']);
        }

        if (! $wallet->is_active) {
            throw ValidationException::withMessages(['wallet' => 'Wallet is inactive.']);
        }

        if ($wallet->is_frozen && $type === 'debit') {
            throw ValidationException::withMessages(['wallet' => 'Wallet is frozen and cannot be spent.']);
        }

        return DB::transaction(function () use ($wallet, $type, $amount, $source, $notes, $performedBy, $reference, $referralCode, $meta) {
            $locked = Wallet::query()->whereKey($wallet->id)->lockForUpdate()->firstOrFail();

            $balance = (float) $locked->balance;
            if ($type === 'debit' && $balance < $amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Insufficient wallet balance. Available: ₹'.number_format($balance, 2),
                ]);
            }

            $newBalance = $type === 'credit' ? $balance + $amount : $balance - $amount;
            $locked->update(['balance' => $newBalance]);

            $txn = WalletTransaction::create([
                'wallet_id' => $locked->id,
                'user_id' => $locked->user_id,
                'created_by' => $locked->created_by,
                'performed_by' => $performedBy?->id,
                'type' => $type,
                'source' => $source,
                'amount' => $amount,
                'balance_after' => $newBalance,
                'status' => 'completed',
                'reference_type' => $reference ? $reference::class : null,
                'reference_id' => $reference?->getKey(),
                'referral_code' => $referralCode,
                'notes' => $notes,
                'meta' => $meta ?: null,
            ]);

            ActivityLogger::log(
                'wallet_'.$type,
                ucfirst($type).' ₹'.number_format($amount, 2).' ('.$txn->sourceLabel().') for '.$locked->user?->name,
                $txn
            );

            return $txn;
        });
    }
}
