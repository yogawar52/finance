<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\BalanceService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(
        BalanceService $balanceService
    ) {
        $user = User::where(
            'email',
            'yoga@example.com'
        )->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Accounts & Balances
        |--------------------------------------------------------------------------
        */

        $accounts = $user->accounts()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $balances = [];

        foreach ($accounts as $account) {
            $balances[$account->id] =
                $balanceService->getBalance($account);
        }

        $totalAssets = array_sum($balances);


        /*
        |--------------------------------------------------------------------------
        | Current Month
        |--------------------------------------------------------------------------
        */

        $startOfMonth = Carbon::now()
            ->startOfMonth();

        $endOfMonth = Carbon::now()
            ->endOfMonth();


        $monthlyTransactions = $user->transactions()
            ->whereBetween(
                'transaction_date',
                [
                    $startOfMonth,
                    $endOfMonth,
                ]
            )
            ->get();


        $income = $monthlyTransactions
            ->where('type', 'income')
            ->sum('amount');


        $expense = $monthlyTransactions
            ->where('type', 'expense')
            ->sum('amount');


        $net = $income - $expense;


        /*
        |--------------------------------------------------------------------------
        | Recent Transactions
        |--------------------------------------------------------------------------
        */

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
                'totalAssets',
                'income',
                'expense',
                'net',
                'recentTransactions'
            )
        );
    }
}
