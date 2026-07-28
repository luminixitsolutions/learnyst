<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Georgia, 'Times New Roman', serif; color: #1e293b; margin: 0; padding: 32px; background: #fff; }
        h1 { font-size: 22px; margin: 0 0 4px; letter-spacing: 0.02em; }
        .muted { color: #64748b; font-size: 13px; }
        .grid { display: flex; justify-content: space-between; gap: 24px; margin: 28px 0; }
        .box { flex: 1; }
        .label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; color: #94a3b8; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 13px; }
        th { text-align: left; border-bottom: 2px solid #e2e8f0; padding: 8px 6px; color: #64748b; font-weight: 600; }
        td { border-bottom: 1px solid #f1f5f9; padding: 10px 6px; }
        .totals { width: 280px; margin-left: auto; margin-top: 20px; font-size: 13px; }
        .totals .row { display: flex; justify-content: space-between; padding: 4px 0; }
        .totals .grand { font-size: 16px; font-weight: 700; border-top: 2px solid #e2e8f0; margin-top: 8px; padding-top: 8px; color: #047857; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #10b981; padding-bottom: 16px; }
        .badge { display: inline-block; padding: 2px 8px; border: 1px solid #a7f3d0; background: #ecfdf5; color: #047857; font-size: 11px; border-radius: 999px; }
        @media print {
            body { padding: 12px; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 16px;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #059669; color: #fff; border: 0; border-radius: 8px; cursor: pointer;">Print</button>
        <a href="{{ route('admin.gst-invoices.show', $invoice) }}" style="margin-left: 8px; color: #047857;">Back</a>
    </div>

    <div class="header">
        <div>
            <h1>{{ $settings['company_name'] }}</h1>
            <p class="muted">
                @if($settings['company_gstin'])GSTIN: {{ $settings['company_gstin'] }} · @endif
                @if($settings['company_state'])State: {{ $settings['company_state'] }}@endif
            </p>
        </div>
        <div style="text-align: right;">
            <div class="badge">Tax Invoice</div>
            <p style="margin: 8px 0 0; font-weight: 700;">{{ $invoice->invoice_number }}</p>
            <p class="muted">Date: {{ $invoice->invoice_date?->format('d M Y') }}</p>
            @if($invoice->order)
                <p class="muted">Order: {{ $invoice->order->order_number }}</p>
            @endif
        </div>
    </div>

    <div class="grid">
        <div class="box">
            <div class="label">Bill To</div>
            <strong>{{ $invoice->billing_name }}</strong><br>
            <span class="muted">{{ $invoice->billing_email }}</span><br>
            @if($invoice->billing_phone)<span class="muted">{{ $invoice->billing_phone }}</span><br>@endif
            @if($invoice->billing_address)<span class="muted">{!! nl2br(e($invoice->billing_address)) !!}</span><br>@endif
            @if($invoice->billing_state)<span class="muted">State: {{ $invoice->billing_state }}</span><br>@endif
            @if($invoice->billing_gstin)<span class="muted">GSTIN: {{ $invoice->billing_gstin }}</span>@endif
        </div>
        <div class="box" style="text-align: right;">
            <div class="label">Place of Supply</div>
            <strong>{{ $invoice->place_of_supply ?: '—' }}</strong>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Discount</th>
                <th>Taxable</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($invoice->order?->items ?? []) as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->course?->title ?? 'Item' }}</td>
                <td>₹{{ number_format($item->price, 2) }}</td>
                <td>₹{{ number_format($item->discount, 2) }}</td>
                <td>₹{{ number_format(max(0, (float)$item->price - (float)$item->discount), 2) }}</td>
            </tr>
            @empty
            <tr>
                <td>1</td>
                <td>Order {{ $invoice->order?->order_number }}</td>
                <td>₹{{ number_format($invoice->subtotal, 2) }}</td>
                <td>₹{{ number_format($invoice->discount, 2) }}</td>
                <td>₹{{ number_format($invoice->taxable_amount, 2) }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="totals">
        <div class="row"><span>Subtotal</span><span>₹{{ number_format($invoice->subtotal, 2) }}</span></div>
        <div class="row"><span>Discount</span><span>-₹{{ number_format($invoice->discount, 2) }}</span></div>
        <div class="row"><span>Taxable Amount</span><span>₹{{ number_format($invoice->taxable_amount, 2) }}</span></div>
        @if((float) $invoice->cgst_amount > 0)
            <div class="row"><span>CGST @ {{ rtrim(rtrim(number_format($invoice->cgst_rate, 2), '0'), '.') }}%</span><span>₹{{ number_format($invoice->cgst_amount, 2) }}</span></div>
            <div class="row"><span>SGST @ {{ rtrim(rtrim(number_format($invoice->sgst_rate, 2), '0'), '.') }}%</span><span>₹{{ number_format($invoice->sgst_amount, 2) }}</span></div>
        @endif
        @if((float) $invoice->igst_amount > 0)
            <div class="row"><span>IGST @ {{ rtrim(rtrim(number_format($invoice->igst_rate, 2), '0'), '.') }}%</span><span>₹{{ number_format($invoice->igst_amount, 2) }}</span></div>
        @endif
        <div class="row grand"><span>Total</span><span>₹{{ number_format($invoice->total, 2) }}</span></div>
    </div>

    @if($invoice->notes)
        <p class="muted" style="margin-top: 32px;">Notes: {{ $invoice->notes }}</p>
    @endif

    <p class="muted" style="margin-top: 40px; font-size: 11px;">This is a computer-generated tax invoice.</p>
</body>
</html>
