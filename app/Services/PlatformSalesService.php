<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PlatformSalesService
{
    /**
     * Map of owner_user_id => Company for quick lookups.
     */
    public function companiesByOwner(): Collection
    {
        return Company::query()
            ->with('subscriptionPackage')
            ->get()
            ->keyBy('owner_user_id');
    }

    public function resolveCompanyForOrder(Order $order, ?Collection $byOwner = null): ?Company
    {
        $byOwner ??= $this->companiesByOwner();

        $order->loadMissing(['items.course', 'user']);

        foreach ($order->items as $item) {
            $ownerId = $item->course?->created_by;
            if ($ownerId && $byOwner->has($ownerId)) {
                return $byOwner->get($ownerId);
            }
        }

        $buyerOwner = $order->user?->created_by;
        if ($buyerOwner && $byOwner->has($buyerOwner)) {
            return $byOwner->get($buyerOwner);
        }

        if ($order->user_id && $byOwner->has($order->user_id)) {
            return $byOwner->get($order->user_id);
        }

        return null;
    }

    public function resolveCompanyForPayment(Payment $payment, ?Collection $byOwner = null): ?Company
    {
        $payment->loadMissing(['order.items.course', 'order.user', 'user']);

        if ($payment->order) {
            return $this->resolveCompanyForOrder($payment->order, $byOwner);
        }

        $byOwner ??= $this->companiesByOwner();
        $buyerOwner = $payment->user?->created_by;
        if ($buyerOwner && $byOwner->has($buyerOwner)) {
            return $byOwner->get($buyerOwner);
        }

        return null;
    }

    public function scopeOrdersForCompany(Builder $query, int $companyId): Builder
    {
        $ownerId = Company::where('id', $companyId)->value('owner_user_id');
        if (! $ownerId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $q) use ($ownerId) {
            $q->whereHas('items.course', fn (Builder $c) => $c->where('created_by', $ownerId))
                ->orWhereHas('user', fn (Builder $u) => $u->where('created_by', $ownerId)->orWhere('id', $ownerId));
        });
    }

    public function scopePaymentsForCompany(Builder $query, int $companyId): Builder
    {
        $ownerId = Company::where('id', $companyId)->value('owner_user_id');
        if (! $ownerId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $q) use ($ownerId) {
            $q->whereHas('order.items.course', fn (Builder $c) => $c->where('created_by', $ownerId))
                ->orWhereHas('order.user', fn (Builder $u) => $u->where('created_by', $ownerId)->orWhere('id', $ownerId))
                ->orWhereHas('user', fn (Builder $u) => $u->where('created_by', $ownerId));
        });
    }
}
