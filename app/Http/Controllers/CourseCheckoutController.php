<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CourseCheckoutController extends Controller
{
    public function __construct(protected RazorpayService $razorpay) {}

    /**
     * Start enrollment: free → grant access; paid → create order + Razorpay checkout.
     */
    public function start(Course $course)
    {
        abort_unless($course->status === 'published', 404);

        $user = Auth::user();
        abort_unless($user && $user->isLearner(), 403);

        if ($this->activeEnrollment($user->id, $course->id)) {
            return redirect()
                ->route('learner.courses.show', $course)
                ->with('success', 'You already have access to this course.');
        }

        if (! $course->requiresPayment()) {
            $this->grantEnrollment($user, $course, null, 'free', 0);

            return redirect()
                ->route('learner.courses.show', $course)
                ->with('success', 'Enrolled successfully. Happy learning!');
        }

        if (! $this->razorpay->isConfigured()) {
            return redirect()
                ->route('public.course', $course)
                ->with('error', 'Payment gateway is not configured yet. Please add Razorpay keys in Platform Admin → Payment / Razorpay (or Institute Settings → Payment).');
        }

        $order = $this->createPendingOrder($user, $course);

        try {
            $checkout = $this->razorpay->createOrder($order);
        } catch (RuntimeException $e) {
            return redirect()
                ->route('public.course', $course)
                ->with('error', $e->getMessage());
        }

        return view('public.course-checkout', [
            'course' => $course,
            'order' => $order,
            'checkout' => $checkout,
            'user' => $user,
        ]);
    }

    /**
     * Verify Razorpay payment and grant course access.
     */
    public function complete(Request $request)
    {
        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        $order = Order::with('items.course')->findOrFail($validated['order_id']);
        abort_unless($order->user_id === Auth::id(), 403);

        if ($order->payment_status === 'paid') {
            $course = $order->items->first()?->course;

            return $course
                ? redirect()->route('learner.courses.show', $course)->with('success', 'Payment already completed.')
                : redirect()->route('learner.dashboard')->with('success', 'Payment already completed.');
        }

        if ($order->gateway_order_id && $order->gateway_order_id !== $validated['razorpay_order_id']) {
            return redirect()
                ->route('public.course', $order->items->first()->course)
                ->with('error', 'Payment verification failed (order mismatch).');
        }

        if (! $this->razorpay->verifySignature(
            $validated['razorpay_order_id'],
            $validated['razorpay_payment_id'],
            $validated['razorpay_signature']
        )) {
            $order->update(['payment_status' => 'failed']);

            return redirect()
                ->route('public.course', $order->items->first()->course)
                ->with('error', 'Payment verification failed. Please try again.');
        }

        $course = null;

        DB::transaction(function () use ($validated, $order, &$course) {
            $order->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'gateway_order_id' => $validated['razorpay_order_id'],
            ]);

            $this->razorpay->recordPayment($order, $validated);

            foreach ($order->items as $item) {
                $course = $item->course;
                $this->grantEnrollment(
                    User::findOrFail($order->user_id),
                    $course,
                    $order,
                    'paid',
                    (float) $item->total
                );
            }

            User::whereKey($order->user_id)->increment('total_spent', (float) $order->total);
        });

        return redirect()
            ->route('learner.courses.show', $course)
            ->with('success', 'Payment successful! You now have access to the course.');
    }

    protected function createPendingOrder(User $user, Course $course): Order
    {
        $amount = $course->payableAmount();
        $listPrice = (float) ($course->price ?? $amount);
        $discount = max(0, $listPrice - $amount);

        return DB::transaction(function () use ($user, $course, $amount, $listPrice, $discount) {
            $order = Order::create([
                'user_id' => $user->id,
                'subtotal' => $listPrice,
                'discount' => $discount,
                'tax' => 0,
                'total' => $amount,
                'payment_status' => 'pending',
                'payment_method' => 'razorpay',
                'notes' => 'Self-checkout: '.$course->title,
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'course_id' => $course->id,
                'price' => $listPrice,
                'discount' => $discount,
                'total' => $amount,
            ]);

            return $order->load('items.course');
        });
    }

    protected function grantEnrollment(
        User $user,
        Course $course,
        ?Order $order,
        string $accessType,
        float $amount
    ): CourseEnrollment {
        $expiresAt = null;
        if ($course->validity_days) {
            $expiresAt = now()->addDays((int) $course->validity_days);
        }

        $wasNew = ! CourseEnrollment::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('enrollment_type', 'course')
            ->exists();

        $enrollment = CourseEnrollment::updateOrCreate(
            [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'enrollment_type' => 'course',
            ],
            [
                'order_id' => $order?->id,
                'status' => 'active',
                'access_type' => $accessType,
                'amount' => $amount,
                'enrolled_at' => now(),
                'access_starts_at' => now(),
                'expires_at' => $expiresAt,
            ]
        );

        if ($wasNew) {
            $course->increment('enrollment_count');
        }

        return $enrollment;
    }

    protected function activeEnrollment(int $userId, int $courseId): ?CourseEnrollment
    {
        return CourseEnrollment::query()
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('status', 'active')
            ->first();
    }
}
