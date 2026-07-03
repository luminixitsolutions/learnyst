<?php

namespace App\Http\Controllers;

use App\Models\CourseEnrollment;
use App\Models\Order;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentGatewayController extends Controller
{
    public function __construct(protected RazorpayService $razorpay) {}

    /**
     * Initiate Razorpay checkout for an order.
     */
    public function initiate(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);
        abort_unless($this->razorpay->isConfigured(), 503, 'Payment gateway not configured.');

        $payload = $this->razorpay->createOrder($order);

        return response()->json([
            'key' => config('services.razorpay.key') ?? $payload,
            'order' => $payload,
        ]);
    }

    /**
     * Handle Razorpay payment callback.
     */
    public function callback(Request $request)
    {
        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        if (!$this->razorpay->verifySignature(
            $validated['razorpay_order_id'],
            $validated['razorpay_payment_id'],
            $validated['razorpay_signature']
        )) {
            return back()->with('error', 'Payment verification failed.');
        }

        DB::transaction(function () use ($validated) {
            $order = Order::findOrFail($validated['order_id']);
            $order->update(['payment_status' => 'paid', 'paid_at' => now()]);
            $this->razorpay->recordPayment($order, $validated);

            foreach ($order->items as $item) {
                CourseEnrollment::updateOrCreate(
                    ['user_id' => $order->user_id, 'course_id' => $item->course_id],
                    ['order_id' => $order->id, 'status' => 'active', 'enrolled_at' => now()]
                );
            }
        });

        return redirect()->route('learner.dashboard')->with('success', 'Payment successful!');
    }
}
