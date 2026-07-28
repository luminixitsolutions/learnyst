<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>{{ $slip->slip_number }}</title>
<style>
body{font-family:Georgia,serif;max-width:720px;margin:40px auto;color:#111}
h1{font-size:22px;margin:0} .muted{color:#666;font-size:13px}
table{width:100%;border-collapse:collapse;margin-top:20px}
td,th{padding:10px;border-bottom:1px solid #ddd;text-align:left}
.total{font-weight:bold;font-size:16px}
@media print{.no-print{display:none}}
</style></head><body>
<button class="no-print" onclick="window.print()">Print / Save as PDF</button>
<h1>Salary Slip</h1>
<p class="muted">{{ $slip->slip_number }} · Period {{ $slip->payrollRun?->periodLabel() }}</p>
<p><strong>{{ $slip->employee?->name }}</strong><br>{{ $slip->employee?->designation }} · {{ $slip->employee?->department }}</p>
<table>
<tr><th>Component</th><th>Amount (₹)</th></tr>
<tr><td>Basic</td><td>{{ number_format($slip->basic_salary,2) }}</td></tr>
<tr><td>HRA</td><td>{{ number_format($slip->hra,2) }}</td></tr>
<tr><td>Allowances</td><td>{{ number_format($slip->allowances,2) }}</td></tr>
<tr><td>Deductions</td><td>-{{ number_format($slip->deductions,2) }}</td></tr>
<tr class="total"><td>Net pay</td><td>{{ number_format($slip->net_pay,2) }}</td></tr>
</table>
<p class="muted">Present days: {{ $slip->present_days }} · Leave days: {{ $slip->leave_days }}</p>
</body></html>
