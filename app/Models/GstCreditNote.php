<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GstCreditNote extends Model
{
    protected $fillable = [
        'created_by', 'gst_invoice_id', 'order_id', 'credit_note_number',
        'credit_note_date', 'amount', 'cgst_amount', 'sgst_amount',
        'igst_amount', 'reason', 'status',
    ];

    protected function casts(): array
    {
        return [
            'credit_note_date' => 'date',
            'amount' => 'decimal:2',
            'cgst_amount' => 'decimal:2',
            'sgst_amount' => 'decimal:2',
            'igst_amount' => 'decimal:2',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function invoice()
    {
        return $this->belongsTo(GstInvoice::class, 'gst_invoice_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function statusLabel(): string
    {
        return ucfirst((string) $this->status);
    }
}
