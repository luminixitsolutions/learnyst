<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\AffiliateCommission;
use App\Models\AffiliateLink;
use App\Models\AffiliatePayout;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AffiliateService
{
    public function isEnabled(): bool
    {
        return filter_var(Setting::get('enabled', '1', 'affiliate'), FILTER_VALIDATE_BOOLEAN);
    }

    public function defaultCommissionPercent(): float
    {
        return (float) Setting::get('default_commission_percent', '10', 'affiliate');
    }

    public function cookieDays(): int
    {
        return (int) Setting::get('cookie_days', '30', 'affiliate');
    }

    public function autoApprove(): bool
    {
        return filter_var(Setting::get('auto_approve', '0', 'affiliate'), FILTER_VALIDATE_BOOLEAN);
    }

    public function createAffiliate(array $data, ?int $createdBy = null): Affiliate
    {
        $status = $this->autoApprove() ? 'approved' : ($data['status'] ?? 'pending');

        $affiliate = Affiliate::create([
            'user_id' => $data['user_id'] ?? null,
            'created_by' => $createdBy,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'code' => ! empty($data['code'])
                ? strtoupper(trim($data['code']))
                : Affiliate::generateUniqueCode($data['name']),
            'status' => $status,
            'commission_type' => $data['commission_type'] ?? 'percent',
            'commission_value' => $data['commission_value'] ?? $this->defaultCommissionPercent(),
            'payment_details' => $data['payment_details'] ?? null,
            'notes' => $data['notes'] ?? null,
            'approved_at' => $status === 'approved' ? now() : null,
        ]);

        ActivityLogger::log('affiliate_created', "Affiliate {$affiliate->name} ({$affiliate->code}) registered", $affiliate);

        return $affiliate;
    }

    public function approveAffiliate(Affiliate $affiliate): Affiliate
    {
        $affiliate->update([
            'status' => 'approved',
            'approved_at' => $affiliate->approved_at ?? now(),
        ]);

        ActivityLogger::log('affiliate_approved', "Affiliate {$affiliate->code} approved", $affiliate);

        return $affiliate->fresh();
    }

    public function rejectAffiliate(Affiliate $affiliate, ?string $notes = null): Affiliate
    {
        $affiliate->update([
            'status' => 'rejected',
            'notes' => $notes ?? $affiliate->notes,
        ]);

        ActivityLogger::log('affiliate_rejected', "Affiliate {$affiliate->code} rejected", $affiliate);

        return $affiliate->fresh();
    }

    public function suspendAffiliate(Affiliate $affiliate, ?string $notes = null): Affiliate
    {
        $affiliate->update([
            'status' => 'suspended',
            'notes' => $notes ?? $affiliate->notes,
        ]);

        ActivityLogger::log('affiliate_suspended', "Affiliate {$affiliate->code} suspended", $affiliate);

        return $affiliate->fresh();
    }

    public function getOrCreateLink(
        Affiliate $affiliate,
        string $productType = 'custom',
        ?int $productId = null,
        ?int $createdBy = null,
        ?string $urlPath = null
    ): AffiliateLink {
        $createdBy = $createdBy ?? $affiliate->created_by;

        $existing = AffiliateLink::where('affiliate_id', $affiliate->id)
            ->where('product_type', $productType)
            ->when($productId, fn ($q) => $q->where('product_id', $productId), fn ($q) => $q->whereNull('product_id'))
            ->first();

        if ($existing) {
            return $existing;
        }

        $slug = AffiliateLink::generateSlug($affiliate, $productType, $productId);
        $path = $urlPath ?: '/a/'.$affiliate->code.'/'.$slug;

        $link = AffiliateLink::create([
            'affiliate_id' => $affiliate->id,
            'created_by' => $createdBy,
            'product_type' => $productType,
            'product_id' => $productId,
            'slug' => $slug,
            'url_path' => $path,
            'is_active' => true,
        ]);

        ActivityLogger::log('affiliate_link_created', "Link {$link->slug} for {$affiliate->code}", $link);

        return $link;
    }

    /** Optional click tracker — increments link + affiliate counters. */
    public function trackClick(?AffiliateLink $link = null, ?string $affiliateCode = null): ?AffiliateLink
    {
        if (! $this->isEnabled()) {
            return null;
        }

        if (! $link && $affiliateCode) {
            $affiliate = Affiliate::where('code', strtoupper(trim($affiliateCode)))
                ->where('status', 'approved')
                ->first();
            $link = $affiliate?->links()->where('is_active', true)->latest()->first();
        }

        if (! $link || ! $link->is_active) {
            return null;
        }

        $link->increment('clicks');
        $link->affiliate?->increment('total_clicks');

        return $link->fresh();
    }

    public function recordConversionFromOrder(Order $order, ?string $affiliateCode = null): ?AffiliateCommission
    {
        if (! $this->isEnabled()) {
            return null;
        }

        $code = strtoupper(trim($affiliateCode ?: ($order->affiliate_code ?? '')));
        if ($code === '') {
            return null;
        }

        $affiliate = Affiliate::where('code', $code)->where('status', 'approved')->first();
        if (! $affiliate) {
            return null;
        }

        $existing = AffiliateCommission::where('affiliate_id', $affiliate->id)
            ->where('order_id', $order->id)
            ->first();
        if ($existing) {
            return $existing;
        }

        return DB::transaction(function () use ($affiliate, $order, $code) {
            if (empty($order->affiliate_code)) {
                $order->update(['affiliate_code' => $code]);
            }

            $link = AffiliateLink::where('affiliate_id', $affiliate->id)
                ->where('is_active', true)
                ->latest()
                ->first();

            $saleAmount = (float) $order->total;
            $rate = (float) $affiliate->commission_value;
            $amount = $affiliate->commission_type === 'fixed'
                ? $rate
                : round($saleAmount * ($rate / 100), 2);

            if ($amount <= 0) {
                return null;
            }

            $commission = AffiliateCommission::create([
                'affiliate_id' => $affiliate->id,
                'affiliate_link_id' => $link?->id,
                'order_id' => $order->id,
                'created_by' => $affiliate->created_by,
                'amount' => $amount,
                'rate' => $rate,
                'status' => 'approved',
                'notes' => 'Commission for order '.$order->order_number,
            ]);

            $affiliate->increment('total_sales', $saleAmount);
            $affiliate->increment('total_commission', $amount);
            $link?->increment('conversions');

            ActivityLogger::log(
                'affiliate_commission_recorded',
                "₹{$amount} commission for {$affiliate->code} on {$order->order_number}",
                $commission
            );

            return $commission;
        });
    }

    public function requestPayout(Affiliate $affiliate, float $amount, ?int $createdBy = null, ?string $notes = null): AffiliatePayout
    {
        if (! $affiliate->isApproved()) {
            throw ValidationException::withMessages(['affiliate' => 'Only approved affiliates can request payouts.']);
        }

        $available = $affiliate->pendingCommissionBalance();
        if ($amount <= 0 || $amount > $available) {
            throw ValidationException::withMessages([
                'amount' => 'Payout must be between 0.01 and available balance (₹'.number_format($available, 2).').',
            ]);
        }

        $payout = AffiliatePayout::create([
            'affiliate_id' => $affiliate->id,
            'created_by' => $createdBy ?? $affiliate->created_by,
            'amount' => $amount,
            'status' => 'pending',
            'notes' => $notes,
        ]);

        ActivityLogger::log('affiliate_payout_requested', "Payout ₹{$amount} for {$affiliate->code}", $payout);

        return $payout;
    }

    public function processPayout(AffiliatePayout $payout, string $status = 'paid', ?string $paymentReference = null, ?string $notes = null): AffiliatePayout
    {
        if (! in_array($status, ['approved', 'paid', 'rejected'], true)) {
            throw ValidationException::withMessages(['status' => 'Invalid payout status.']);
        }

        if ($payout->status === 'paid') {
            return $payout;
        }

        return DB::transaction(function () use ($payout, $status, $paymentReference, $notes) {
            $affiliate = Affiliate::query()->whereKey($payout->affiliate_id)->lockForUpdate()->firstOrFail();

            $payout->update([
                'status' => $status,
                'payment_reference' => $paymentReference ?? $payout->payment_reference,
                'notes' => $notes ?? $payout->notes,
                'paid_at' => $status === 'paid' ? now() : $payout->paid_at,
            ]);

            if ($status === 'paid') {
                $affiliate->increment('paid_commission', (float) $payout->amount);
            }

            ActivityLogger::log(
                'affiliate_payout_'.$status,
                "Payout #{$payout->id} marked {$status} for {$affiliate->code}",
                $payout
            );

            return $payout->fresh();
        });
    }
}
