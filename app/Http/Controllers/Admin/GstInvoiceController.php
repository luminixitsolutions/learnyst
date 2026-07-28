<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\GstInvoice;
use App\Models\Order;
use App\Services\GstInvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class GstInvoiceController extends Controller
{
    use ScopesToCurrentUser;

    public function __construct(protected GstInvoiceService $invoices) {}

    public function index(Request $request)
    {
        $query = $this->owned(GstInvoice::query())
            ->with(['user', 'order'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('invoice_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('invoice_date', '<=', $request->to_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('billing_name', 'like', "%{$search}%")
                    ->orWhere('billing_gstin', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('order', fn ($o) => $o->where('order_number', 'like', "%{$search}%"));
            });
        }

        $invoices = $query->paginate(20)->withQueryString();

        $stats = [
            'count' => (clone $this->owned(GstInvoice::query()))->count(),
            'issued' => (clone $this->owned(GstInvoice::query()))->where('status', 'issued')->count(),
            'total' => (clone $this->owned(GstInvoice::query()))->where('status', 'issued')->sum('total'),
            'tax' => (clone $this->owned(GstInvoice::query()))->where('status', 'issued')
                ->selectRaw('COALESCE(SUM(cgst_amount + sgst_amount + igst_amount), 0) as tax_sum')
                ->value('tax_sum'),
        ];

        return view('admin.gst-invoices.index', compact('invoices', 'stats'));
    }

    public function show(GstInvoice $gstInvoice)
    {
        $this->authorizeOwner($gstInvoice);
        $gstInvoice->load(['user', 'order.items.course', 'creditNotes', 'creator']);

        $settings = $this->invoices->taxSettings();

        return view('admin.gst-invoices.show', [
            'invoice' => $gstInvoice,
            'settings' => $settings,
        ]);
    }

    public function generate(Request $request, Order $order)
    {
        $this->authorizeOrderAccess($order);

        $validated = $request->validate([
            'billing_name' => ['nullable', 'string', 'max:255'],
            'billing_email' => ['nullable', 'email', 'max:255'],
            'billing_phone' => ['nullable', 'string', 'max:50'],
            'billing_address' => ['nullable', 'string'],
            'billing_state' => ['nullable', 'string', 'max:100'],
            'billing_gstin' => ['nullable', 'string', 'max:20'],
            'place_of_supply' => ['nullable', 'string', 'max:100'],
        ]);

        try {
            $invoice = $this->invoices->generateForOrder($order, Auth::id(), $validated);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return redirect()
            ->route('admin.gst-invoices.show', $invoice)
            ->with('success', 'GST invoice generated.');
    }

    public function download(GstInvoice $gstInvoice)
    {
        $this->authorizeOwner($gstInvoice);
        $gstInvoice->load(['user', 'order.items.course']);
        $settings = $this->invoices->taxSettings();

        return view('admin.gst-invoices.print', [
            'invoice' => $gstInvoice,
            'settings' => $settings,
        ]);
    }

    public function creditNote(Request $request, GstInvoice $gstInvoice)
    {
        $this->authorizeOwner($gstInvoice);

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->invoices->createCreditNote($gstInvoice, $validated['reason'] ?? null, Auth::id());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', 'Credit note created.');
    }

    protected function authorizeOrderAccess(Order $order): void
    {
        abort_unless($this->ownedOrdersQuery()->whereKey($order->id)->exists(), 403);
    }
}
