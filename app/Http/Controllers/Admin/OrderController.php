<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CheckoutConsent;
use App\Models\Coupon;
use App\Models\OrderConsent;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\AffiliateService;
use App\Services\FinanceService;
use App\Services\GstInvoiceService;
use App\Services\ReferralService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    use ScopesToCurrentUser;

    public function __construct(
        protected WalletService $wallets,
        protected ReferralService $referrals,
        protected AffiliateService $affiliates,
        protected GstInvoiceService $gstInvoices,
        protected FinanceService $finance,
    ) {}

    protected function authorizeOrder(Order $order): void
    {
        abort_unless($this->ownedOrdersQuery()->whereKey($order->id)->exists(), 403);
    }

    public function index(Request $request)
    {
        $query = $this->ownedOrdersQuery()->with(['user', 'items.course'])->latest();

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%' . $request->search . '%'));
            });
        }

        $orders = $query->limit(500)->get();

        return view('admin.orders.index', compact('orders'));
    }

    public function create()
    {
        $learners = $this->visibleLearnersQuery()->orderBy('name')->get();
        $courses = $this->owned(Course::query())->where('status', 'published')->orderBy('title')->get();
        $coupons = Coupon::where('is_active', true)->get();
        $consents = CheckoutConsent::active()->get();

        return view('admin.orders.create', compact('learners', 'courses', 'coupons', 'consents'));
    }

    public function store(Request $request)
    {
        $ownedLearnerIds = $this->visibleLearnersQuery()->pluck('id');
        $ownedCourseIds = $this->ownedCourseIds();

        $validated = $request->validate([
            'user_id' => ['required', Rule::in($ownedLearnerIds)],
            'course_ids' => ['required', 'array', 'min:1'],
            'course_ids.*' => [Rule::in($ownedCourseIds)],
            'coupon_id' => ['nullable', 'exists:coupons,id'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:razorpay,manual,free,wallet'],
            'payment_status' => ['required', 'in:pending,paid,failed'],
            'wallet_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'affiliate_code' => ['nullable', 'string', 'max:40'],
            'consents' => ['nullable', 'array'],
            'consents.*' => ['boolean'],
        ]);

        DB::transaction(function () use ($validated, $request) {
            $courses = Course::whereIn('id', $validated['course_ids'])->get();
            $subtotal = $courses->sum('price');
            $discount = $validated['discount'] ?? 0;
            $tax = ($subtotal - $discount) * 0.18;
            $total = max(0, $subtotal - $discount + $tax);
            $walletAmount = 0.0;

            $learner = User::findOrFail($validated['user_id']);
            $wallet = null;

            if ($validated['payment_method'] === 'wallet' || (float) ($validated['wallet_amount'] ?? 0) > 0) {
                $wallet = $this->wallets->getOrCreateForLearner($learner, Auth::id());
                $walletAmount = $validated['payment_method'] === 'wallet'
                    ? $total
                    : min((float) $validated['wallet_amount'], $total);

                if ($walletAmount > 0 && ! $wallet->canSpend($walletAmount)) {
                    throw ValidationException::withMessages([
                        'wallet_amount' => 'Insufficient or frozen wallet balance. Available: ₹'.number_format((float) $wallet->balance, 2),
                    ]);
                }
            }

            $activeConsents = CheckoutConsent::active()->get();
            foreach ($activeConsents as $consent) {
                if ($consent->is_required && !$request->boolean("consents.{$consent->id}")) {
                    throw ValidationException::withMessages([
                        'consents' => "Required consent \"{$consent->title}\" must be accepted.",
                    ]);
                }
            }

            $order = Order::create([
                'user_id' => $validated['user_id'],
                'coupon_id' => $validated['coupon_id'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'wallet_amount' => $walletAmount,
                'tax' => $tax,
                'total' => $total,
                'payment_status' => $validated['payment_status'],
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
                'affiliate_code' => ! empty($validated['affiliate_code'])
                    ? strtoupper(trim($validated['affiliate_code']))
                    : null,
                'paid_at' => $validated['payment_status'] === 'paid' ? now() : null,
            ]);

            foreach ($courses as $course) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'course_id' => $course->id,
                    'price' => $course->price,
                    'discount' => 0,
                    'total' => $course->price,
                ]);

                if ($validated['payment_status'] === 'paid') {
                    CourseEnrollment::updateOrCreate(
                        ['user_id' => $validated['user_id'], 'course_id' => $course->id, 'enrollment_type' => 'course'],
                        ['order_id' => $order->id, 'status' => 'active', 'enrolled_at' => now(), 'access_starts_at' => now()]
                    );
                }
            }

            if ($validated['payment_status'] === 'paid') {
                if ($wallet && $walletAmount > 0) {
                    $this->wallets->spendOnOrder($wallet, $order, $walletAmount, Auth::user());
                }

                Payment::create([
                    'order_id' => $order->id,
                    'user_id' => $validated['user_id'],
                    'gateway' => $validated['payment_method'],
                    'amount' => $total,
                    'status' => 'success',
                    'paid_at' => now(),
                ]);

                $payment = Payment::where('order_id', $order->id)->latest()->first();
                if ($payment) {
                    $this->finance->syncIncomeFromPayment($payment, Auth::id());
                }

                User::find($validated['user_id'])->increment('total_spent', $total);
                if (! empty($validated['coupon_id'])) {
                    Coupon::whereKey($validated['coupon_id'])->increment('used_count');
                }
                $this->referrals->markQualifiedFromOrder($order);
                $this->affiliates->recordConversionFromOrder(
                    $order,
                    $validated['affiliate_code'] ?? $order->affiliate_code
                );
            }

            $activeConsents = CheckoutConsent::active()->get();
            foreach ($activeConsents as $consent) {
                $accepted = $request->boolean("consents.{$consent->id}");
                OrderConsent::create([
                    'order_id' => $order->id,
                    'checkout_consent_id' => $consent->id,
                    'user_id' => $validated['user_id'],
                    'accepted' => $accepted,
                    'accepted_at' => $accepted ? now() : null,
                    'ip_address' => $request->ip(),
                ]);
            }

            ActivityLogger::log('order_created', "Order {$order->order_number} created", $order);
        });

        return redirect()->route('admin.orders.index')->with('success', 'Order created successfully.');
    }

    public function show(Order $order)
    {
        $this->authorizeOrder($order);
        $order->load(['user', 'items.course', 'payments', 'coupon', 'gstInvoice']);

        return view('admin.orders.show', compact('order'));
    }

    public function invoice(Order $order)
    {
        $this->authorizeOrder($order);
        $order->load(['user', 'items.course']);

        return view('admin.orders.invoice', compact('order'));
    }

    public function refund(Request $request, Order $order)
    {
        $this->authorizeOrder($order);

        if ($order->payment_status === 'refunded') {
            return back()->with('success', 'Order already refunded.');
        }

        DB::transaction(function () use ($order) {
            $order->update([
                'payment_status' => 'refunded',
                'refund_status' => 'processed',
            ]);

            $this->wallets->creditRefund($order, Auth::user());

            $invoice = $this->gstInvoices->findForOrder($order);
            if ($invoice) {
                try {
                    $this->gstInvoices->createCreditNote($invoice, 'Order refund '.$order->order_number, Auth::id());
                } catch (ValidationException) {
                    // Credit note may already exist for this invoice.
                }
            }

            ActivityLogger::log('order_refunded', "Order {$order->order_number} refunded", $order);
        });

        return back()->with('success', 'Refund processed' . ($this->wallets->refundToWallet() ? ' and credited to wallet.' : '.'));
    }
}
