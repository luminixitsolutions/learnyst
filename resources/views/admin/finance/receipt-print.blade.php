<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>{{ $receipt->receipt_number }}</title>
<style>body{font-family:system-ui,sans-serif;max-width:640px;margin:40px auto;color:#111} .muted{color:#666;font-size:13px} table{width:100%;border-collapse:collapse;margin-top:24px} td{padding:8px 0;border-bottom:1px solid #eee} @media print{.no-print{display:none}}</style>
</head><body>
<button class="no-print" onclick="window.print()">Print</button>
<h1>Payment Receipt</h1>
<p class="muted">This is a payment acknowledgement. GST tax invoices use INV- numbering separately.</p>
<table>
<tr><td>Receipt No</td><td><strong>{{ $receipt->receipt_number }}</strong></td></tr>
<tr><td>Date</td><td>{{ $receipt->receipt_date->format('d M Y') }}</td></tr>
<tr><td>Received from</td><td>{{ $receipt->payer_name ?? $receipt->user?->name }}</td></tr>
<tr><td>Amount</td><td><strong>₹{{ number_format($receipt->amount, 2) }}</strong></td></tr>
<tr><td>Mode</td><td>{{ $receipt->payment_mode ?? '—' }}</td></tr>
<tr><td>Order</td><td>{{ $receipt->order?->order_number ?? '—' }}</td></tr>
<tr><td>Notes</td><td>{{ $receipt->notes }}</td></tr>
</table>
</body></html>
