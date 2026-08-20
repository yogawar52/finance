<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\BalanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(
        Request $request,
        BalanceService $balanceService
    ) {
        $user = User::where(
            'email',
            'yoga@example.com'
        )->firstOrFail();

        $month = $request->input(
            'month',
            now()->format('Y-m')
        );

        try {

            $selectedMonth = Carbon::createFromFormat(
                'Y-m',
                $month
            );
        } catch (\Exception $e) {

            $selectedMonth = now();

            $month = $selectedMonth->format('Y-m');
        }

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

        $startOfMonth = $selectedMonth
            ->copy()
            ->startOfMonth();

        $endOfMonth = $selectedMonth
            ->copy()
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
        | Income vs Expense Chart
        |--------------------------------------------------------------------------
        */

        $monthlyChart = collect();

        for ($i = 5; $i >= 0; $i--) {

            $month = $selectedMonth
                ->copy()
                ->subMonths($i);

            $start = $month->copy()
                ->startOfMonth();

            $end = $month->copy()
                ->endOfMonth();

            $transactions = $user->transactions()
                ->whereBetween(
                    'transaction_date',
                    [
                        $start,
                        $end,
                    ]
                )
                ->get();

            $monthlyChart->push([
                'label' => $month->format('M Y'),

                'income' => (float) $transactions
                    ->where('type', 'income')
                    ->sum('amount'),

                'expense' => (float) $transactions
                    ->where('type', 'expense')
                    ->sum('amount'),
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Expense by Category
        |--------------------------------------------------------------------------
        */

        $expenseByCategory = $monthlyTransactions
            ->where('type', 'expense')
            ->groupBy('category_id')
            ->map(function ($items) {
                return $items->sum('amount');
            });


        $categories = $user->categories()
            ->with('parent')
            ->get()
            ->keyBy('id');


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
                'recentTransactions',
                'monthlyChart',
                'expenseByCategory',
                'categories',
                'month'
            )
        );
    }
}
