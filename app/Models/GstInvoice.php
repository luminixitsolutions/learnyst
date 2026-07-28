<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GstInvoice extends Model
{
    protected $fillable = [
        'created_by', 'order_id', 'user_id', 'invoice_number', 'invoice_date',
        'billing_name', 'billing_email', 'billing_phone', 'billing_address',
        'billing_state', 'billing_gstin', 'place_of_supply',
        'subtotal', 'discount', 'taxable_amount',
        'cgst_rate', 'cgst_amount', 'sgst_rate', 'sgst_amount',
        'igst_rate', 'igst_amount', 'total',
        'status', 'pdf_path', 'notes', 'meta',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'taxable_amount' => 'decimal:2',
            'cgst_rate' => 'decimal:2',
            'cgst_amount' => 'decimal:2',
            'sgst_rate' => 'decimal:2',
            'sgst_amount' => 'decimal:2',
            'igst_rate' => 'decimal:2',
            'igst_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'meta' => 'array',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creditNotes()
    {
        return $this->hasMany(GstCreditNote::class);
    }

    public function isIntraState(): bool
    {
        return (float) $this->igst_amount <= 0 && ((float) $this->cgst_amount > 0 || (float) $this->sgst_amount > 0);
    }

    public function taxTotal(): float
    {
        return (float) $this->cgst_amount + (float) $this->sgst_amount + (float) $this->igst_amount;
    }

    public function statusLabel(): string
    {
        return ucfirst((string) $this->status);
    }
}
