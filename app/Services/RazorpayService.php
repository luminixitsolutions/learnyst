<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Razorpay payment gateway integration.
 * Configure keys in Admin → Settings → Payment, or .env (RAZORPAY_KEY / RAZORPAY_SECRET).
 */
class RazorpayService
{
    protected ?string $keyId;

    protected ?string $keySecret;

    public function __construct()
    {
        $this->keyId = Setting::get('razorpay_key', '', 'payment')
            ?: config('services.razorpay.key');
        $this->keySecret = Setting::get('razorpay_secret', '', 'payment')
            ?: config('services.razorpay.secret');
    }

    public function keyId(): ?string
    {
        return $this->keyId ?: null;
    }

    public function isConfigured(): bool
    {
        return filled($this->keyId) && filled($this->keySecret);
    }

    /**
     * Create a Razorpay order via API and return checkout payload.
     */
    public function createOrder(Order $order): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Payment gateway is not configured.');
        }

        $amountPaise = (int) round(((float) $order->total) * 100);
        if ($amountPaise < 100) {
            throw new RuntimeException('Order amount must be at least ₹1.');
        }

        $currency = Setting::get('currency', 'INR', 'payment') ?: 'INR';

        $response = Http::withBasicAuth($this->keyId, $this->keySecret)
            ->acceptJson()
            ->post('https://api.razorpay.com/v1/orders', [
                'amount' => $amountPaise,
                'currency' => $currency,
                'receipt' => $order->order_number,
                'notes' => [
                    'order_id' => (string) $order->id,
                    'user_id' => (string) $order->user_id,
                ],
            ]);

        if (! $response->successful()) {
            throw new RuntimeException(
                'Unable to create Razorpay order: '.($response->json('error.description') ?? $response->body())
            );
        }

        $gatewayOrderId = $response->json('id');
        $order->update(['gateway_order_id' => $gatewayOrderId]);

        return [
            'key' => $this->keyId,
            'amount' => $amountPaise,
            'currency' => $currency,
            'order_id' => $gatewayOrderId,
            'receipt' => $order->order_number,
            'name' => config('website.brand', config('app.name', 'StudyNest')),
            'description' => 'Order '.$order->order_number,
            'local_order_id' => $order->id,
        ];
    }

    public function verifySignature(string $orderId, string $paymentId, string $signature): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        $payload = $orderId.'|'.$paymentId;
        $expected = hash_hmac('sha256', $payload, $this->keySecret);

        return hash_equals($expected, $signature);
    }

    public function recordPayment(Order $order, array $gatewayData): Payment
    {
        return Payment::updateOrCreate(
            [
                'transaction_id' => $gatewayData['razorpay_payment_id'] ?? null,
            ],
            [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'gateway' => 'razorpay',
                'amount' => $order->total,
                'status' => 'success',
                'gateway_response' => $gatewayData,
                'paid_at' => now(),
            ]
        );
    }
}
