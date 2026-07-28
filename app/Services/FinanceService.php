<?php

namespace App\Services;

use App\Models\FinanceAccount;
use App\Models\FinanceLedgerEntry;
use App\Models\FinanceReceipt;
use App\Models\GstInvoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class FinanceService
{
    public function ensureDefaultAccounts(int $instituteUserId): void
    {
        FinanceAccount::firstOrCreate(
            ['created_by' => $instituteUserId, 'name' => 'Cash'],
            ['type' => 'cash', 'opening_balance' => 0, 'is_active' => true]
        );
        FinanceAccount::firstOrCreate(
            ['created_by' => $instituteUserId, 'name' => 'Primary Bank'],
            ['type' => 'bank', 'opening_balance' => 0, 'is_active' => true]
        );
    }

    public function nextReceiptNumber(int $instituteUserId): string
    {
        $key = 'receipt_next_'.$instituteUserId;
        $next = (int) Setting::get($key, 1, 'finance');
        Setting::set($key, $next + 1, 'finance');

        return 'RCPT-'.date('Y').'-'.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    public function syncIncomeFromPayment(Payment $payment, int $instituteUserId): ?FinanceLedgerEntry
    {
        if ($payment->status !== 'success') {
            return null;
        }

        $existing = FinanceLedgerEntry::where('payment_id', $payment->id)->first();
        if ($existing) {
            return $existing;
        }

        $this->ensureDefaultAccounts($instituteUserId);
        $account = FinanceAccount::where('created_by', $instituteUserId)
            ->where('type', 'bank')
            ->where('is_active', true)
            ->first()
            ?: FinanceAccount::where('created_by', $instituteUserId)->where('is_active', true)->first();

        $order = $payment->order;
        $gstInvoice = $order ? GstInvoice::where('order_id', $order->id)->whereIn('status', ['draft', 'issued'])->latest()->first() : null;

        return DB::transaction(function () use ($payment, $instituteUserId, $account, $order, $gstInvoice) {
            $entry = FinanceLedgerEntry::create([
                'created_by' => $instituteUserId,
                'finance_account_id' => $account?->id,
                'entry_type' => 'income',
                'category' => 'course_sales',
                'title' => 'Payment '.($payment->transaction_id ?: '#'.$payment->id),
                'description' => $order ? 'Order '.$order->order_number : null,
                'amount' => $payment->amount,
                'entry_date' => ($payment->paid_at ?: now())->toDateString(),
                'payment_mode' => $payment->gateway,
                'reference' => $payment->transaction_id,
                'order_id' => $payment->order_id,
                'payment_id' => $payment->id,
                'gst_invoice_id' => $gstInvoice?->id,
            ]);

            $receiptNo = $this->nextReceiptNumber($instituteUserId);
            $entry->update(['receipt_number' => $receiptNo]);

            FinanceReceipt::create([
                'created_by' => $instituteUserId,
                'finance_ledger_entry_id' => $entry->id,
                'order_id' => $payment->order_id,
                'user_id' => $payment->user_id,
                'receipt_number' => $receiptNo,
                'receipt_date' => $entry->entry_date,
                'payer_name' => $order?->user?->name,
                'amount' => $payment->amount,
                'payment_mode' => $payment->gateway,
                'notes' => $gstInvoice
                    ? 'Linked GST invoice '.$gstInvoice->invoice_number.' (GST numbering unchanged).'
                    : 'Payment receipt (non-GST acknowledgement).',
            ]);

            return $entry;
        });
    }

    public function profitAndLoss(int $instituteUserId, ?string $from = null, ?string $to = null): array
    {
        $q = FinanceLedgerEntry::where('created_by', $instituteUserId);
        if ($from) {
            $q->whereDate('entry_date', '>=', $from);
        }
        if ($to) {
            $q->whereDate('entry_date', '<=', $to);
        }

        $income = (clone $q)->where('entry_type', 'income')->sum('amount');
        $expense = (clone $q)->where('entry_type', 'expense')->sum('amount');
        $byIncomeCat = (clone $q)->where('entry_type', 'income')
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')->pluck('total', 'category');
        $byExpenseCat = (clone $q)->where('entry_type', 'expense')
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')->pluck('total', 'category');

        return [
            'income' => (float) $income,
            'expense' => (float) $expense,
            'profit' => (float) $income - (float) $expense,
            'income_by_category' => $byIncomeCat,
            'expense_by_category' => $byExpenseCat,
        ];
    }

    public function balanceSheetSummary(int $instituteUserId): array
    {
        $accounts = FinanceAccount::where('created_by', $instituteUserId)->where('is_active', true)->get();
        $cash = 0.0;
        $bank = 0.0;
        foreach ($accounts as $account) {
            $bal = $account->balance();
            if ($account->type === 'cash') {
                $cash += $bal;
            } elseif ($account->type === 'bank') {
                $bank += $bal;
            } else {
                $cash += $bal;
            }
        }

        $pnl = $this->profitAndLoss($instituteUserId);

        return [
            'assets' => [
                'cash' => $cash,
                'bank' => $bank,
                'total' => $cash + $bank,
            ],
            'equity' => [
                'retained_earnings' => $pnl['profit'],
                'total' => $pnl['profit'],
            ],
        ];
    }

    public function taxExportRows(int $instituteUserId, ?string $from = null, ?string $to = null): array
    {
        $q = GstInvoice::where('created_by', $instituteUserId)->where('status', 'issued');
        if ($from) {
            $q->whereDate('invoice_date', '>=', $from);
        }
        if ($to) {
            $q->whereDate('invoice_date', '<=', $to);
        }

        return $q->orderBy('invoice_date')->get()->map(fn (GstInvoice $inv) => [
            'invoice_number' => $inv->invoice_number,
            'invoice_date' => optional($inv->invoice_date)->format('Y-m-d'),
            'taxable' => $inv->taxable_amount,
            'cgst' => $inv->cgst_amount,
            'sgst' => $inv->sgst_amount,
            'igst' => $inv->igst_amount,
            'total' => $inv->total,
        ])->all();
    }
}
