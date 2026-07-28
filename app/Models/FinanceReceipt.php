<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceReceipt extends Model
{
    protected $fillable = [
        'created_by', 'finance_ledger_entry_id', 'order_id', 'user_id',
        'receipt_number', 'receipt_date', 'payer_name', 'amount',
        'payment_mode', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'receipt_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function entry()
    {
        return $this->belongsTo(FinanceLedgerEntry::class, 'finance_ledger_entry_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
