<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $order->order_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; color: #1e293b; padding: 40px; max-width: 800px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 2px solid #10b981; }
        .logo { font-size: 24px; font-weight: 700; color: #059669; }
        .logo img { height: 48px; width: auto; display: block; object-fit: contain; }
        .invoice-title { font-size: 28px; font-weight: 700; text-align: right; }
        .meta { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px; }
        .meta h4 { font-size: 12px; text-transform: uppercase; color: #64748b; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #f8fafc; font-size: 12px; text-transform: uppercase; color: #64748b; }
        .totals { margin-left: auto; width: 280px; }
        .totals div { display: flex; justify-content: space-between; padding: 8px 0; }
        .totals .grand { font-size: 18px; font-weight: 700; color: #059669; border-top: 2px solid #e2e8f0; padding-top: 12px; margin-top: 8px; }
        .footer { margin-top: 40px; text-align: center; color: #94a3b8; font-size: 12px; }
        @media print { body { padding: 20px; } .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #059669; color: white; border: none; border-radius: 8px; cursor: pointer;">Print Invoice</button>
        <a href="{{ route('admin.orders.show', $order) }}" style="margin-left: 10px; color: #64748b;">Back to Order</a>
    </div>

    <div class="header">
        <div class="logo">
            <img src="{{ \App\Support\Brand::logoUrl() }}" alt="{{ \App\Support\Brand::name() }}">
        </div>
        <div>
            <div class="invoice-title">INVOICE</div>
            <p style="text-align: right; color: #64748b; margin-top: 4px;">#{{ $order->order_number }}</p>
        </div>
    </div>

    <div class="meta">
        <div>
            <h4>Bill To</h4>
            <p><strong>{{ $order->user?->name }}</strong></p>
            <p>{{ $order->user?->email }}</p>
            @if($order->user?->phone)<p>{{ $order->user->phone }}</p>@endif
        </div>
        <div style="text-align: right;">
            <h4>Invoice Details</h4>
            <p>Date: {{ $order->created_at->format('M d, Y') }}</p>
            <p>Status: {{ ucfirst($order->payment_status) }}</p>
            @if($order->paid_at)<p>Paid: {{ $order->paid_at->format('M d, Y') }}</p>@endif
        </div>
    </div>

    <table>
        <thead>
            <tr><th>Description</th><th>Price</th><th>Discount</th><th>Total</th></tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->course?->title }}</td>
                <td>₹{{ number_format($item->price, 2) }}</td>
                <td>₹{{ number_format($item->discount, 2) }}</td>
                <td>₹{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div><span>Subtotal</span><span>₹{{ number_format($order->subtotal, 2) }}</span></div>
        <div><span>Discount</span><span>-₹{{ number_format($order->discount, 2) }}</span></div>
        <div><span>Tax (18%)</span><span>₹{{ number_format($order->tax, 2) }}</span></div>
        <div class="grand"><span>Total</span><span>₹{{ number_format($order->total, 2) }}</span></div>
    </div>

    <div class="footer">
        <p>Thank you for your purchase!</p>
        <p>{{ config('app.name') }} · Generated {{ now()->format('M d, Y') }}</p>
    </div>
</body>
</html>
