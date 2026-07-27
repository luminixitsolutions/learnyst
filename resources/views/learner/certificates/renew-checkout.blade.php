@extends('layouts.app')

@section('title', 'Certificate Renewal Payment')
@section('page-title', 'Complete Renewal Payment')

@section('content')
<div class="max-w-lg mx-auto glass-card rounded-2xl p-8 space-y-6 text-center">
    <p class="text-slate-600">Pay <strong>₹{{ number_format($order->total, 2) }}</strong> to renew certificate <span class="font-mono">{{ $certificate->certificate_number }}</span>.</p>

    <form id="renew-complete-form" method="POST" action="{{ $completeRoute ?? route('learner.certificates.renew.complete') }}">
        @csrf
        <input type="hidden" name="order_id" value="{{ $order->id }}">
        <input type="hidden" name="certificate_id" value="{{ $certificate->id }}">
        <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
        <input type="hidden" name="razorpay_signature" id="razorpay_signature">
        <button type="button" id="rzp-pay" class="panel-btn-primary w-full justify-center">Pay with Razorpay</button>
    </form>
    <a href="{{ auth()->user()->isAlumni() ? route('alumni.certificates.renew', $certificate) : route('learner.certificates.renew', $certificate) }}" class="text-sm text-slate-500 hover:text-indigo-600">← Back</a>
</div>

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.getElementById('rzp-pay').addEventListener('click', function () {
    const options = {
        key: @json($checkout['key']),
        amount: @json($checkout['amount']),
        currency: @json($checkout['currency'] ?? 'INR'),
        name: @json(config('app.name')),
        description: 'Certificate renewal',
        order_id: @json($checkout['order_id']),
        prefill: {
            name: @json($user->name),
            email: @json($user->email),
        },
        handler: function (response) {
            document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
            document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
            document.getElementById('razorpay_signature').value = response.razorpay_signature;
            document.getElementById('renew-complete-form').submit();
        }
    };
    new Razorpay(options).open();
});
</script>
@endpush
@endsection
