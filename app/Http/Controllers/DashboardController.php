<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\BalanceService;

class DashboardController extends Controller
{
    public function index(BalanceService $balanceService)
    {
        $user = User::where('email', 'yoga@example.com')->firstOrFail();

        $accounts = $user->accounts()
            ->where('is_active', true)
            ->get();

        $accountBalances = $accounts->mapWithKeys(function ($account) use ($balanceService) {
            return [
                $account->id => $balanceService->getBalance($account),
            ];
        });

        $totalBalance = $accountBalances->sum();

        $transactions = $user->transactions()
            ->with(['account', 'destinationAccount', 'category'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'accounts',
            'accountBalances',
            'totalBalance',
            'transactions'
        ));
    }
}
