<?php

namespace App\Services;

use App\Models\GstCreditNote;
use App\Models\GstInvoice;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GstInvoiceService
{
    public function taxSettings(): array
    {
        $cgst = (float) Setting::get('cgst_rate', Setting::get('gst_rate', '18', 'tax') / 2, 'tax');
        $sgst = (float) Setting::get('sgst_rate', Setting::get('gst_rate', '18', 'tax') / 2, 'tax');
        $igst = (float) Setting::get('igst_rate', Setting::get('gst_rate', '18', 'tax'), 'tax');

        return [
            'cgst_rate' => $cgst,
            'sgst_rate' => $sgst,
            'igst_rate' => $igst,
            'gst_rate' => (float) Setting::get('gst_rate', '18', 'tax'),
            'company_gstin' => (string) Setting::get('company_gstin', '', 'tax'),
            'company_state' => (string) Setting::get('company_state', '', 'tax'),
            'invoice_prefix' => (string) Setting::get('invoice_prefix', 'INV', 'tax'),
            'invoice_next_number' => (int) Setting::get('invoice_next_number', '1', 'tax'),
            'company_name' => (string) Setting::get('site_name', 'StudyNest', 'general'),
        ];
    }

    public function nextInvoiceNumber(): string
    {
        $settings = $this->taxSettings();
        $prefix = $settings['invoice_prefix'] ?: 'INV';
        $next = max(1, (int) $settings['invoice_next_number']);
        $year = now()->format('Y');

        $number = sprintf('%s-%s-%05d', strtoupper($prefix), $year, $next);
        Setting::set('invoice_next_number', (string) ($next + 1), 'tax', 'number');

        return $number;
    }

    public function nextCreditNoteNumber(): string
    {
        $prefix = (string) Setting::get('credit_note_prefix', 'CN', 'tax') ?: 'CN';
        $next = max(1, (int) Setting::get('credit_note_next_number', '1', 'tax'));
        $year = now()->format('Y');

        $number = sprintf('%s-%s-%05d', strtoupper($prefix), $year, $next);
        Setting::set('credit_note_next_number', (string) ($next + 1), 'tax', 'number');

        return $number;
    }

    public function generateForOrder(Order $order, ?int $createdBy = null, array $billing = []): GstInvoice
    {
        if ($order->payment_status !== 'paid') {
            throw ValidationException::withMessages(['order' => 'GST invoice can only be generated for paid orders.']);
        }

        $existing = GstInvoice::where('order_id', $order->id)
            ->whereIn('status', ['draft', 'issued'])
            ->first();

        if ($existing) {
            throw ValidationException::withMessages(['order' => 'A GST invoice already exists for this order.']);
        }

        $order->loadMissing('user');
        $user = $order->user;
        $settings = $this->taxSettings();
        $createdBy = $createdBy ?? Auth::id() ?? $user?->created_by;

        $billingName = $billing['billing_name'] ?? $user?->name;
        $billingEmail = $billing['billing_email'] ?? $user?->email;
        $billingPhone = $billing['billing_phone'] ?? $user?->phone;
        $billingAddress = $billing['billing_address'] ?? $user?->address;
        $billingState = $billing['billing_state'] ?? ($billing['place_of_supply'] ?? null);
        $billingGstin = $billing['billing_gstin'] ?? null;
        $placeOfSupply = $billing['place_of_supply'] ?? $billingState;

        $subtotal = (float) $order->subtotal;
        $discount = (float) $order->discount;
        $taxable = max(0, $subtotal - $discount);

        $companyState = strtolower(trim((string) $settings['company_state']));
        $buyerState = strtolower(trim((string) ($placeOfSupply ?: $billingState)));
        $sameState = $companyState !== '' && $buyerState !== '' && $companyState === $buyerState;

        // If buyer state unknown, default to IGST (inter-state).
        if ($companyState === '' || $buyerState === '') {
            $sameState = false;
        }

        $cgstRate = $sgstRate = $igstRate = 0.0;
        $cgstAmount = $sgstAmount = $igstAmount = 0.0;

        if ($sameState) {
            $cgstRate = (float) $settings['cgst_rate'];
            $sgstRate = (float) $settings['sgst_rate'];
            $cgstAmount = round($taxable * $cgstRate / 100, 2);
            $sgstAmount = round($taxable * $sgstRate / 100, 2);
        } else {
            $igstRate = (float) $settings['igst_rate'];
            $igstAmount = round($taxable * $igstRate / 100, 2);
        }

        $total = round($taxable + $cgstAmount + $sgstAmount + $igstAmount, 2);

        return DB::transaction(function () use (
            $order, $user, $createdBy, $billingName, $billingEmail, $billingPhone,
            $billingAddress, $billingState, $billingGstin, $placeOfSupply,
            $subtotal, $discount, $taxable, $cgstRate, $cgstAmount, $sgstRate,
            $sgstAmount, $igstRate, $igstAmount, $total, $settings, $sameState
        ) {
            $invoice = GstInvoice::create([
                'created_by' => $createdBy,
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'invoice_number' => $this->nextInvoiceNumber(),
                'invoice_date' => now()->toDateString(),
                'billing_name' => $billingName,
                'billing_email' => $billingEmail,
                'billing_phone' => $billingPhone,
                'billing_address' => $billingAddress,
                'billing_state' => $billingState,
                'billing_gstin' => $billingGstin,
                'place_of_supply' => $placeOfSupply,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'taxable_amount' => $taxable,
                'cgst_rate' => $cgstRate,
                'cgst_amount' => $cgstAmount,
                'sgst_rate' => $sgstRate,
                'sgst_amount' => $sgstAmount,
                'igst_rate' => $igstRate,
                'igst_amount' => $igstAmount,
                'total' => $total,
                'status' => 'issued',
                'meta' => [
                    'company_gstin' => $settings['company_gstin'],
                    'company_state' => $settings['company_state'],
                    'tax_mode' => $sameState ? 'cgst_sgst' : 'igst',
                    'order_number' => $order->order_number,
                ],
            ]);

            ActivityLogger::log(
                'gst_invoice_generated',
                "GST invoice {$invoice->invoice_number} generated for order {$order->order_number}",
                $invoice
            );

            return $invoice;
        });
    }

    public function createCreditNote(GstInvoice $invoice, ?string $reason = null, ?int $createdBy = null): GstCreditNote
    {
        if ($invoice->status === 'cancelled') {
            throw ValidationException::withMessages(['invoice' => 'Cannot create credit note for a cancelled invoice.']);
        }

        $existing = GstCreditNote::where('gst_invoice_id', $invoice->id)
            ->where('status', 'issued')
            ->first();

        if ($existing) {
            throw ValidationException::withMessages(['invoice' => 'A credit note already exists for this invoice.']);
        }

        return DB::transaction(function () use ($invoice, $reason, $createdBy) {
            $note = GstCreditNote::create([
                'created_by' => $createdBy ?? Auth::id() ?? $invoice->created_by,
                'gst_invoice_id' => $invoice->id,
                'order_id' => $invoice->order_id,
                'credit_note_number' => $this->nextCreditNoteNumber(),
                'credit_note_date' => now()->toDateString(),
                'amount' => $invoice->total,
                'cgst_amount' => $invoice->cgst_amount,
                'sgst_amount' => $invoice->sgst_amount,
                'igst_amount' => $invoice->igst_amount,
                'reason' => $reason ?? 'Order refund',
                'status' => 'issued',
            ]);

            ActivityLogger::log(
                'gst_credit_note_created',
                "Credit note {$note->credit_note_number} created for invoice {$invoice->invoice_number}",
                $note
            );

            return $note;
        });
    }

    public function findForOrder(Order $order): ?GstInvoice
    {
        return GstInvoice::where('order_id', $order->id)
            ->whereIn('status', ['draft', 'issued'])
            ->latest('id')
            ->first();
    }
}
