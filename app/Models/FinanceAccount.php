<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceAccount extends Model
{
    protected $fillable = [
        'created_by', 'name', 'type', 'bank_name', 'account_number',
        'ifsc', 'opening_balance', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'opening_balance' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function entries()
    {
        return $this->hasMany(FinanceLedgerEntry::class);
    }

    public function balance(): float
    {
        $income = (float) $this->entries()->where('entry_type', 'income')->sum('amount');
        $expense = (float) $this->entries()->where('entry_type', 'expense')->sum('amount');

        return (float) $this->opening_balance + $income - $expense;
    }
}
