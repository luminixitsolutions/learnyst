<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceLedgerEntry extends Model
{
    protected $fillable = [
        'created_by', 'finance_account_id', 'entry_type', 'category', 'title',
        'description', 'amount', 'entry_date', 'payment_mode', 'reference',
        'order_id', 'payment_id', 'gst_invoice_id', 'receipt_number', 'meta',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'entry_date' => 'date',
            'meta' => 'array',
        ];
    }

    public function account()
    {
        return $this->belongsTo(FinanceAccount::class, 'finance_account_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function gstInvoice()
    {
        return $this->belongsTo(GstInvoice::class);
    }

    public function receipt()
    {
        return $this->hasOne(FinanceReceipt::class);
    }

    public static function incomeCategories(): array
    {
        return ['course_sales', 'subscriptions', 'other_income', 'refund_reversal'];
    }

    public static function expenseCategories(): array
    {
        return ['salary', 'rent', 'utilities', 'marketing', 'software', 'refund', 'other_expense'];
    }
}
