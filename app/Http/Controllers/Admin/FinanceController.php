<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\FinanceAccount;
use App\Models\FinanceLedgerEntry;
use App\Models\FinanceReceipt;
use App\Models\GstInvoice;
use App\Models\Order;
use App\Models\Payment;
use App\Services\ActivityLogger;
use App\Services\FinanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FinanceController extends Controller
{
    use ScopesToCurrentUser;

    public function __construct(protected FinanceService $finance) {}

    public function dashboard(Request $request)
    {
        $this->finance->ensureDefaultAccounts(Auth::id());
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to = $request->get('to', now()->toDateString());
        $pnl = $this->finance->profitAndLoss(Auth::id(), $from, $to);
        $sheet = $this->finance->balanceSheetSummary(Auth::id());
        $recent = $this->owned(FinanceLedgerEntry::query())->with('account')->latest('entry_date')->limit(10)->get();
        $gstCount = $this->owned(GstInvoice::query())->where('status', 'issued')->count();

        return view('admin.finance.dashboard', compact('pnl', 'sheet', 'recent', 'from', 'to', 'gstCount'));
    }

    public function ledger(Request $request)
    {
        $query = $this->owned(FinanceLedgerEntry::query())->with(['account', 'order']);
        if ($request->filled('type')) {
            $query->where('entry_type', $request->type);
        }
        if ($request->filled('from')) {
            $query->whereDate('entry_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('entry_date', '<=', $request->to);
        }
        $entries = $query->latest('entry_date')->paginate(30)->withQueryString();
        $accounts = $this->owned(FinanceAccount::query())->where('is_active', true)->orderBy('name')->get();

        return view('admin.finance.ledger', compact('entries', 'accounts'));
    }

    public function storeEntry(Request $request)
    {
        $validated = $request->validate([
            'entry_type' => ['required', 'in:income,expense'],
            'finance_account_id' => ['nullable', Rule::in($this->owned(FinanceAccount::query())->pluck('id')->all())],
            'category' => ['nullable', 'string', 'max:80'],
            'title' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'entry_date' => ['required', 'date'],
            'payment_mode' => ['nullable', 'string', 'max:40'],
            'reference' => ['nullable', 'string', 'max:120'],
            'order_id' => ['nullable', 'integer'],
        ]);

        if (! empty($validated['order_id'])) {
            abort_unless($this->ownedOrdersQuery()->whereKey($validated['order_id'])->exists(), 403);
        }

        $validated['created_by'] = Auth::id();
        $entry = FinanceLedgerEntry::create($validated);
        ActivityLogger::log('finance_entry_created', "Finance {$entry->entry_type} {$entry->title}", $entry);

        return back()->with('success', 'Ledger entry saved.');
    }

    public function destroyEntry(FinanceLedgerEntry $entry)
    {
        $this->authorizeOwner($entry);
        $entry->delete();

        return back()->with('success', 'Entry deleted.');
    }

    public function accounts()
    {
        $this->finance->ensureDefaultAccounts(Auth::id());
        $accounts = $this->owned(FinanceAccount::query())->orderBy('type')->orderBy('name')->get();

        return view('admin.finance.accounts', compact('accounts'));
    }

    public function storeAccount(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'type' => ['required', 'in:cash,bank,other'],
            'bank_name' => ['nullable', 'string', 'max:120'],
            'account_number' => ['nullable', 'string', 'max:60'],
            'ifsc' => ['nullable', 'string', 'max:20'],
            'opening_balance' => ['nullable', 'numeric'],
        ]);
        $validated['created_by'] = Auth::id();
        $validated['opening_balance'] = $validated['opening_balance'] ?? 0;
        FinanceAccount::create($validated);

        return back()->with('success', 'Account created.');
    }

    public function cashBook(Request $request)
    {
        return $this->bookView('cash', $request);
    }

    public function bankBook(Request $request)
    {
        return $this->bookView('bank', $request);
    }

    protected function bookView(string $type, Request $request)
    {
        $this->finance->ensureDefaultAccounts(Auth::id());
        $accounts = $this->owned(FinanceAccount::query())->where('type', $type)->where('is_active', true)->get();
        $accountId = $request->integer('account_id') ?: $accounts->first()?->id;
        $account = $accountId ? $this->owned(FinanceAccount::query())->whereKey($accountId)->first() : null;

        $entries = collect();
        if ($account) {
            $q = $account->entries()->latest('entry_date');
            if ($request->filled('from')) {
                $q->whereDate('entry_date', '>=', $request->from);
            }
            if ($request->filled('to')) {
                $q->whereDate('entry_date', '<=', $request->to);
            }
            $entries = $q->paginate(40)->withQueryString();
        }

        return view('admin.finance.book', [
            'type' => $type,
            'accounts' => $accounts,
            'account' => $account,
            'entries' => $entries,
            'balance' => $account?->balance() ?? 0,
        ]);
    }

    public function receipts()
    {
        $receipts = $this->owned(FinanceReceipt::query())->with(['order', 'user'])->latest('receipt_date')->paginate(30);

        return view('admin.finance.receipts', compact('receipts'));
    }

    public function showReceipt(FinanceReceipt $receipt)
    {
        $this->authorizeOwner($receipt);
        $receipt->load(['order', 'user', 'entry']);

        return view('admin.finance.receipt-print', compact('receipt'));
    }

    public function syncFromOrders()
    {
        $payments = Payment::where('status', 'success')
            ->whereIn('order_id', $this->ownedOrdersQuery()->pluck('id'))
            ->whereNotIn('id', FinanceLedgerEntry::whereNotNull('payment_id')->pluck('payment_id'))
            ->latest()
            ->limit(200)
            ->get();

        $count = 0;
        foreach ($payments as $payment) {
            if ($this->finance->syncIncomeFromPayment($payment, Auth::id())) {
                $count++;
            }
        }

        return back()->with('success', "Synced {$count} payment(s) into income ledger. GST invoices remain the tax documents.");
    }

    public function profitLoss(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to = $request->get('to', now()->toDateString());
        $pnl = $this->finance->profitAndLoss(Auth::id(), $from, $to);

        return view('admin.finance.profit-loss', compact('pnl', 'from', 'to'));
    }

    public function balanceSheet()
    {
        $sheet = $this->finance->balanceSheetSummary(Auth::id());

        return view('admin.finance.balance-sheet', compact('sheet'));
    }

    public function taxExport(Request $request): StreamedResponse
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $rows = $this->finance->taxExportRows(Auth::id(), $from, $to);

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Invoice Number', 'Date', 'Taxable', 'CGST', 'SGST', 'IGST', 'Total']);
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row['invoice_number'], $row['invoice_date'], $row['taxable'],
                    $row['cgst'], $row['sgst'], $row['igst'], $row['total'],
                ]);
            }
            fclose($out);
        }, 'tax-report-'.now()->format('Ymd').'.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function invoices()
    {
        // Redirect helper into existing GST module — do not duplicate numbering UI
        return redirect()->route('admin.gst-invoices.index')
            ->with('success', 'GST invoices use the existing tax numbering (INV-YYYY-#####). Finance receipts are separate acknowledgements.');
    }
}
