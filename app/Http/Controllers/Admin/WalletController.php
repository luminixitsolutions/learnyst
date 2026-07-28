<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\ActivityLogger;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class WalletController extends Controller
{
    use ScopesToCurrentUser;

    public function __construct(protected WalletService $wallets) {}

    public function index(Request $request)
    {
        $query = $this->owned(Wallet::query())
            ->with(['user'])
            ->withCount('transactions')
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            match ($request->status) {
                'frozen' => $query->where('is_frozen', true),
                'inactive' => $query->where('is_active', false),
                'active' => $query->where('is_active', true)->where('is_frozen', false),
                default => null,
            };
        }

        $wallets = $query->paginate(20)->withQueryString();

        $stats = [
            'total_balance' => (clone $this->owned(Wallet::query()))->sum('balance'),
            'wallet_count' => (clone $this->owned(Wallet::query()))->count(),
            'frozen_count' => (clone $this->owned(Wallet::query()))->where('is_frozen', true)->count(),
            'credits_month' => $this->owned(WalletTransaction::query())
                ->where('type', 'credit')
                ->where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
            'debits_month' => $this->owned(WalletTransaction::query())
                ->where('type', 'debit')
                ->where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
        ];

        return view('admin.wallets.index', compact('wallets', 'stats'));
    }

    public function create()
    {
        $existingIds = $this->owned(Wallet::query())->pluck('user_id');
        $learners = $this->visibleLearnersQuery()
            ->whereNotIn('id', $existingIds)
            ->orderBy('name')
            ->get();

        return view('admin.wallets.create', compact('learners'));
    }

    public function store(Request $request)
    {
        $ownedLearnerIds = $this->visibleLearnersQuery()->pluck('id');

        $validated = $request->validate([
            'user_id' => ['required', Rule::in($ownedLearnerIds)],
            'opening_balance' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $learner = User::findOrFail($validated['user_id']);
        $wallet = $this->wallets->getOrCreateForLearner($learner, Auth::id());

        if (! empty($validated['notes'])) {
            $wallet->update(['notes' => $validated['notes']]);
        }

        $opening = (float) ($validated['opening_balance'] ?? 0);
        if ($opening > 0) {
            $this->wallets->topUp($wallet, $opening, 'Opening balance', Auth::user());
        }

        ActivityLogger::log('wallet_created', "Wallet created for {$learner->name}", $wallet);

        return redirect()
            ->route('admin.wallets.show', $wallet)
            ->with('success', 'Wallet created successfully.');
    }

    public function show(Wallet $wallet)
    {
        $this->authorizeOwner($wallet);
        $wallet->load('user');

        $transactions = $wallet->transactions()
            ->with('performer')
            ->latest()
            ->paginate(25);

        return view('admin.wallets.show', compact('wallet', 'transactions'));
    }

    public function adjust(Request $request, Wallet $wallet)
    {
        $this->authorizeOwner($wallet);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'not_in:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->wallets->adjust($wallet, (float) $validated['amount'], $validated['notes'] ?? null, Auth::user());

        return back()->with('success', 'Wallet balance adjusted.');
    }

    public function topUp(Request $request, Wallet $wallet)
    {
        $this->authorizeOwner($wallet);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->wallets->topUp($wallet, (float) $validated['amount'], $validated['notes'] ?? 'Manual top-up', Auth::user());

        return back()->with('success', 'Wallet topped up successfully.');
    }

    public function freeze(Request $request, Wallet $wallet)
    {
        $this->authorizeOwner($wallet);

        $validated = $request->validate([
            'freeze' => ['required', 'boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->wallets->freeze($wallet, $request->boolean('freeze'), $validated['notes'] ?? null);

        return back()->with('success', $request->boolean('freeze') ? 'Wallet frozen.' : 'Wallet unfrozen.');
    }

    public function settings()
    {
        $settings = Setting::where('group', 'wallet')->get()->pluck('value', 'key');

        return view('admin.wallets.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'enabled' => ['nullable', 'boolean'],
            'allow_checkout_redeem' => ['nullable', 'boolean'],
            'refund_to_wallet' => ['nullable', 'boolean'],
            'signup_bonus' => ['nullable', 'numeric', 'min:0'],
            'min_topup' => ['nullable', 'numeric', 'min:0'],
        ]);

        Setting::set('enabled', $request->boolean('enabled') ? '1' : '0', 'wallet');
        Setting::set('allow_checkout_redeem', $request->boolean('allow_checkout_redeem') ? '1' : '0', 'wallet');
        Setting::set('refund_to_wallet', $request->boolean('refund_to_wallet') ? '1' : '0', 'wallet');
        Setting::set('signup_bonus', (string) ($validated['signup_bonus'] ?? 0), 'wallet');
        Setting::set('min_topup', (string) ($validated['min_topup'] ?? 0), 'wallet');

        ActivityLogger::log('wallet_settings_updated', 'Wallet settings updated');

        return back()->with('success', 'Wallet settings saved.');
    }

    public function transactions(Request $request)
    {
        $query = $this->owned(WalletTransaction::query())
            ->with(['user', 'wallet', 'performer'])
            ->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('referral_code', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $transactions = $query->paginate(25)->withQueryString();

        return view('admin.wallets.transactions', compact('transactions'));
    }
}
