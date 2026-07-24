@extends('website.layouts.app')

@section('title', 'Checkout – '.$course->title)
@section('meta_description', 'Complete payment for '.$course->title)

@section('content')
<section class="ly-auth-hero">
    <div class="ly-container ly-auth-hero-inner">
        <p class="ly-product-eyebrow">Secure checkout</p>
        <h1>Complete payment</h1>
        <p class="ly-product-lead">Pay securely with Razorpay to unlock <strong>{{ $course->title }}</strong>.</p>
    </div>
</section>

<section class="ly-section ly-section-soft">
    <div class="ly-container" style="max-width:640px">
        <div class="ly-auth-card">
            <h3>{{ $course->title }}</h3>
            <p class="ly-auth-card-lead">Order {{ $order->order_number }}</p>

            <div class="ly-course-price-block" style="margin:18px 0">
                <div class="ly-course-price">
                    <strong>{{ $course->displayPrice() }}</strong>
                    @if($course->hasDiscount())
                        <s>₹{{ number_format((float) $course->price, 0) }}</s>
                    @endif
                </div>
                <p>Paid access via Razorpay</p>
            </div>

            @if(session('error'))
                <div class="ly-auth-error">{{ session('error') }}</div>
            @endif

            <button type="button" id="lyPayBtn" class="ly-btn ly-btn-green ly-auth-submit">
                Pay {{ $course->displayPrice() }} with Razorpay
            </button>

            <div class="ly-auth-links" style="margin-top:16px">
                <p><a href="{{ route('public.course', $course) }}">← Back to course</a></p>
            </div>
        </div>
    </div>
</section>

<form id="lyRazorpayForm" method="POST" action="{{ route('courses.checkout.complete') }}" class="d-none" hidden>
    @csrf
    <input type="hidden" name="order_id" value="{{ $order->id }}">
    <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
    <input type="hidden" name="razorpay_signature" id="razorpay_signature">
</form>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
(function () {
    var options = {
        key: @json($checkout['key']),
        amount: @json($checkout['amount']),
        currency: @json($checkout['currency']),
        name: @json($checkout['name']),
        description: @json($course->title),
        order_id: @json($checkout['order_id']),
        prefill: {
            name: @json($user->name),
            email: @json($user->email),
            contact: @json($user->phone ?? '')
        },
        theme: { color: '#15803d' },
        handler: function (response) {
            document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
            document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
            document.getElementById('razorpay_signature').value = response.razorpay_signature;
            document.getElementById('lyRazorpayForm').submit();
        },
        modal: {
            ondismiss: function () {
                // Stay on checkout page so the learner can retry.
            }
        }
    };

    var rzp = new Razorpay(options);

    function openCheckout() {
        rzp.open();
    }

    document.getElementById('lyPayBtn').addEventListener('click', openCheckout);
    // Auto-open Razorpay on load for faster checkout.
    openCheckout();
})();
</script>
@endsection
