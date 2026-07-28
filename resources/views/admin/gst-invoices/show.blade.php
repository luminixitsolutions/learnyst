@extends('layouts.app')

@section('title', $invoice->invoice_number)
@section('page-title', 'GST Invoice ' . $invoice->invoice_number)
@section('breadcrumb', 'Sales / GST Invoices / Details')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <x-badge :type="match($invoice->status) { 'issued' => 'success', 'cancelled' => 'danger', default => 'info' }">{{ $invoice->statusLabel() }}</x-badge>
            <p class="text-sm text-slate-500 mt-2">{{ $invoice->invoice_date?->format('M d, Y') }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.gst-invoices.index') }}" class="panel-btn-secondary">Back</a>
            <a href="{{ route('admin.gst-invoices.download', $invoice) }}" class="panel-btn-primary" target="_blank">Print / Download</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 glass-card rounded-2xl p-6 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
                <div>
                    <h3 class="font-bold text-slate-800 mb-2">Bill To</h3>
                    <p class="text-slate-800 font-medium">{{ $invoice->billing_name }}</p>
                    <p class="text-slate-500">{{ $invoice->billing_email }}</p>
                    <p class="text-slate-500">{{ $invoice->billing_phone }}</p>
                    <p class="text-slate-500 whitespace-pre-line">{{ $invoice->billing_address }}</p>
                    @if($invoice->billing_state)<p class="text-slate-500">State: {{ $invoice->billing_state }}</p>@endif
                    @if($invoice->billing_gstin)<p class="text-slate-500">GSTIN: {{ $invoice->billing_gstin }}</p>@endif
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 mb-2">Supplier</h3>
                    <p class="text-slate-800 font-medium">{{ $settings['company_name'] }}</p>
                    @if($settings['company_gstin'])<p class="text-slate-500">GSTIN: {{ $settings['company_gstin'] }}</p>@endif
                    @if($settings['company_state'])<p class="text-slate-500">State: {{ $settings['company_state'] }}</p>@endif
                    <p class="text-slate-500 mt-3">Place of supply: {{ $invoice->place_of_supply ?: '—' }}</p>
                    <p class="text-slate-500">Order: @if($invoice->order)<a href="{{ route('admin.orders.show', $invoice->order) }}" class="text-emerald-700">{{ $invoice->order->order_number }}</a>@else — @endif</p>
                </div>
            </div>

            @if($invoice->order?->items?->count())
            <div class="overflow-x-auto border-t border-slate-200 pt-4">
                <table class="w-full text-sm panel-table">
                    <thead>
                        <tr class="text-left text-slate-500">
                            <th class="pb-2">Item</th>
                            <th class="pb-2">Price</th>
                            <th class="pb-2">Discount</th>
                            <th class="pb-2">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->order->items as $item)
                        <tr>
                            <td class="py-2.5 text-slate-800">{{ $item->course?->title }}</td>
                            <td class="py-2.5">₹{{ number_format($item->price, 2) }}</td>
                            <td class="py-2.5">₹{{ number_format($item->discount, 2) }}</td>
                            <td class="py-2.5">₹{{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <dl class="space-y-2 text-sm border-t border-slate-200 pt-4">
                <div class="flex justify-between"><dt class="text-slate-500">Subtotal</dt><dd>₹{{ number_format($invoice->subtotal, 2) }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Discount</dt><dd>-₹{{ number_format($invoice->discount, 2) }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Taxable</dt><dd>₹{{ number_format($invoice->taxable_amount, 2) }}</dd></div>
                @if((float) $invoice->cgst_amount > 0)
                <div class="flex justify-between"><dt class="text-slate-500">CGST ({{ rtrim(rtrim(number_format($invoice->cgst_rate, 2), '0'), '.') }}%)</dt><dd>₹{{ number_format($invoice->cgst_amount, 2) }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">SGST ({{ rtrim(rtrim(number_format($invoice->sgst_rate, 2), '0'), '.') }}%)</dt><dd>₹{{ number_format($invoice->sgst_amount, 2) }}</dd></div>
                @endif
                @if((float) $invoice->igst_amount > 0)
                <div class="flex justify-between"><dt class="text-slate-500">IGST ({{ rtrim(rtrim(number_format($invoice->igst_rate, 2), '0'), '.') }}%)</dt><dd>₹{{ number_format($invoice->igst_amount, 2) }}</dd></div>
                @endif
                <div class="flex justify-between font-semibold text-lg"><dt>Total</dt><dd class="text-emerald-700">₹{{ number_format($invoice->total, 2) }}</dd></div>
            </dl>
        </div>

        <div class="space-y-6">
            <div class="glass-card rounded-2xl p-6 space-y-3">
                <h3 class="text-lg font-bold text-slate-800">Credit Note</h3>
                @if($invoice->creditNotes->where('status', 'issued')->isNotEmpty())
                    @foreach($invoice->creditNotes as $note)
                        <div class="text-sm border border-slate-200 rounded-xl p-3">
                            <p class="font-medium text-slate-800">{{ $note->credit_note_number }}</p>
                            <p class="text-slate-500">{{ $note->credit_note_date?->format('M d, Y') }} · ₹{{ number_format($note->amount, 2) }}</p>
                            <p class="text-slate-500 mt-1">{{ $note->reason }}</p>
                        </div>
                    @endforeach
                @elseif($invoice->status === 'issued')
                <form method="POST" action="{{ route('admin.gst-invoices.credit-note', $invoice) }}" class="space-y-3" onsubmit="return confirm('Create credit note for full invoice amount?')">
                    @csrf
                    <input type="text" name="reason" placeholder="Reason" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    <button type="submit" class="panel-btn-secondary w-full">Create Credit Note</button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
