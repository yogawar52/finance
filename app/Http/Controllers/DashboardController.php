<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\BalanceService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(BalanceService $balanceService)
    {
        $user = User::where(
            'email',
            'yoga@example.com'
        )->firstOrFail();

        $accounts = $user->accounts()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $balances = [];

        foreach ($accounts as $account) {
            $balances[$account->id] =
                $balanceService->getBalance($account);
        }

        $startOfMonth = Carbon::now()
            ->startOfMonth();

        $endOfMonth = Carbon::now()
            ->endOfMonth();

        $income = $user->transactions()
            ->where('type', 'income')
            ->whereBetween(
                'transaction_date',
                [
                    $startOfMonth,
                    $endOfMonth,
                ]
            )
            ->sum('amount');

        $expense = $user->transactions()
            ->where('type', 'expense')
            ->whereBetween(
                'transaction_date',
                [
                    $startOfMonth,
                    $endOfMonth,
                ]
            )
            ->sum('amount');

        $recentTransactions = $user->transactions()
            ->with([
                'account',
                'destinationAccount',
                'category',
            ])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view(
            'dashboard',
            compact(
                'accounts',
                'balances',
                'income',
                'expense',
                'recentTransactions'
            )
        );
    }
}
