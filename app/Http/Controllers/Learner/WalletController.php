<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Services\WalletService;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function __construct(protected WalletService $wallets) {}

    public function index()
    {
        $user = Auth::user();
        $wallet = $this->wallets->getOrCreateForLearner($user, $user->created_by ? (int) $user->created_by : Auth::id());

        $transactions = $wallet->transactions()->latest()->paginate(20);

        return view('learner.wallet.index', compact('wallet', 'transactions'));
    }
}
