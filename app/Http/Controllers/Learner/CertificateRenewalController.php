<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\ActivityLogger;
use App\Services\CertificateLifecycleService;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CertificateRenewalController extends Controller
{
    public function __construct(
        protected CertificateLifecycleService $lifecycle,
        protected RazorpayService $razorpay,
    ) {}

    protected function certificatesRoutePrefix(): string
    {
        return auth()->user()?->isAlumni() ? 'alumni' : 'learner';
    }

    protected function certificatesIndexRoute(): string
    {
        return route($this->certificatesRoutePrefix() . '.certificates');
    }

    protected function certificatesDownloadRoute(Certificate $certificate): string
    {
        if (auth()->user()?->isAlumni()) {
            return route('learner.certificates.download', $certificate);
        }

        return route('learner.certificates.download', $certificate);
    }

    protected function certificatesRenewRoute(Certificate $certificate): string
    {
        return route($this->certificatesRoutePrefix() . '.certificates.renew', $certificate);
    }

    public function show(Certificate $certificate)
    {
        $this->authorizeCertificate($certificate);

        if (! $this->lifecycle->isRenewable($certificate)) {
            return redirect()
                ->to($this->certificatesIndexRoute())
                ->with('error', 'This certificate does not require renewal yet.');
        }

        $certificate->load(['course', 'template']);
        $price = $this->lifecycle->renewalPrice($certificate);

        return view('learner.certificates.renew', compact('certificate', 'price'));
    }

    public function start(Certificate $certificate)
    {
        $this->authorizeCertificate($certificate);

        if (! $this->lifecycle->isRenewable($certificate)) {
            return back()->with('error', 'This certificate does not require renewal yet.');
        }

        $price = $this->lifecycle->renewalPrice($certificate);

        if ($price <= 0) {
            $renewed = $this->lifecycle->renew($certificate);
            ActivityLogger::log('certificate_renewed', "Free renewal for {$renewed->certificate_number}", $renewed);

            return redirect()
                ->route('learner.certificates.download', $renewed)
                ->with('success', 'Certificate renewed successfully.');
        }

        if (! $this->razorpay->isConfigured()) {
            return back()->with('error', 'Payment gateway is not configured. Contact your institute.');
        }

        $order = DB::transaction(function () use ($certificate, $price) {
            $tax = round($price * 0.18, 2);
            $total = $price + $tax;

            $order = Order::create([
                'user_id' => Auth::id(),
                'subtotal' => $price,
                'tax' => $tax,
                'total' => $total,
                'payment_status' => 'pending',
                'payment_method' => 'razorpay',
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'item_type' => 'certificate_renewal',
                'course_id' => $certificate->course_id,
                'certificate_id' => $certificate->id,
                'price' => $price,
                'total' => $price,
            ]);

            return $order;
        });

        try {
            $checkout = $this->razorpay->createOrder($order);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return view('learner.certificates.renew-checkout', [
            'certificate' => $certificate,
            'order' => $order,
            'checkout' => $checkout,
            'user' => Auth::user(),
            'completeRoute' => route($this->certificatesRoutePrefix() . '.certificates.renew.complete'),
        ]);
    }

    public function complete(Request $request)
    {
        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'certificate_id' => ['required', 'exists:certificates,id'],
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        $certificate = Certificate::findOrFail($validated['certificate_id']);
        $this->authorizeCertificate($certificate);

        $order = Order::with('items')->findOrFail($validated['order_id']);
        abort_unless($order->user_id === Auth::id(), 403);

        if ($order->payment_status === 'paid') {
            $renewed = Certificate::where('renewed_from_id', $certificate->id)->latest('id')->first();

            return $renewed
                ? redirect()->route('learner.certificates.download', $renewed)->with('success', 'Certificate already renewed.')
                : redirect()->to($this->certificatesIndexRoute())->with('success', 'Payment already completed.');
        }

        if (! $this->razorpay->verifySignature(
            $validated['razorpay_order_id'],
            $validated['razorpay_payment_id'],
            $validated['razorpay_signature']
        )) {
            $order->update(['payment_status' => 'failed']);

            return redirect()
                ->to($this->certificatesRenewRoute($certificate))
                ->with('error', 'Payment verification failed.');
        }

        $renewed = null;

        DB::transaction(function () use ($validated, $order, $certificate, &$renewed) {
            $order->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'gateway_order_id' => $validated['razorpay_order_id'],
            ]);

            $this->razorpay->recordPayment($order, $validated);
            $renewed = $this->lifecycle->renew($certificate);
        });

        ActivityLogger::log('certificate_renewed', "Paid renewal for {$renewed->certificate_number}", $renewed);

        return redirect()
            ->route('learner.certificates.download', $renewed)
            ->with('success', 'Certificate renewed successfully.');
    }

    protected function authorizeCertificate(Certificate $certificate): void
    {
        abort_unless((int) $certificate->user_id === (int) Auth::id(), 403);
    }
}
