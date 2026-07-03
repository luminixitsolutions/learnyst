<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;

/**
 * Razorpay payment gateway integration structure.
 * Configure keys in Admin → Settings → Payment.
 */
class RazorpayService
{
    protected ?string $keyId;
    protected ?string $keySecret;

    public function __construct()
    {
        $this->keyId = Setting::get('razorpay_key', '', 'payment');
        $this->keySecret = Setting::get('razorpay_secret', '', 'payment');
    }

    public function isConfigured(): bool
    {
        return filled($this->keyId) && filled($this->keySecret);
    }

    /**
     * Create a Razorpay order payload (integrate with razorpay/razorpay-php SDK).
     */
    public function createOrder(Order $order): array
    {
        return [
            'amount' => (int) ($order->total * 100),
            'currency' => Setting::get('currency', 'INR', 'payment'),
            'receipt' => $order->order_number,
            'notes' => [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
            ],
        ];
    }

    /**
     * Verify payment signature after checkout callback.
     */
    public function verifySignature(string $orderId, string $paymentId, string $signature): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $payload = $orderId . '|' . $paymentId;
        $expected = hash_hmac('sha256', $payload, $this->keySecret);

        return hash_equals($expected, $signature);
    }

    /**
     * Record successful Razorpay payment.
     */
    public function recordPayment(Order $order, array $gatewayData): Payment
    {
        return Payment::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'transaction_id' => $gatewayData['razorpay_payment_id'] ?? null,
            'gateway' => 'razorpay',
            'amount' => $order->total,
            'status' => 'success',
            'gateway_response' => $gatewayData,
            'paid_at' => now(),
        ]);
    }
}
