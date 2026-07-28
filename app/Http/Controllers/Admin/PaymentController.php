<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use ScopesToCurrentUser;

    protected function ownedPaymentsQuery()
    {
        return Payment::query()->whereIn(
            'order_id',
            $this->ownedOrdersQuery()->select('orders.id')
        );
    }

    protected function authorizePayment(Payment $payment): void
    {
        abort_unless($this->ownedPaymentsQuery()->whereKey($payment->id)->exists(), 403);
    }

    public function index(Request $request)
    {
        $query = $this->ownedPaymentsQuery()->with(['user', 'order'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('gateway')) {
            $query->where('gateway', $request->gateway);
        }

        $payments = $query->limit(500)->get();
        $failedCount = $this->ownedPaymentsQuery()->where('status', 'failed')->count();
        $totalReceived = $this->ownedPaymentsQuery()->where('status', 'success')->sum('amount');

        return view('admin.payments.index', compact('payments', 'failedCount', 'totalReceived'));
    }

    public function show(Payment $payment)
    {
        $this->authorizePayment($payment);
        $payment->load(['user', 'order.items.course']);

        return view('admin.payments.show', compact('payment'));
    }
}
