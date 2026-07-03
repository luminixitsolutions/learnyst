<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'order'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('gateway')) {
            $query->where('gateway', $request->gateway);
        }

        $payments = $query->paginate(20)->withQueryString();
        $failedCount = Payment::where('status', 'failed')->count();
        $totalReceived = Payment::where('status', 'success')->sum('amount');

        return view('admin.payments.index', compact('payments', 'failedCount', 'totalReceived'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['user', 'order.items.course']);

        return view('admin.payments.show', compact('payment'));
    }
}
