<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\BalanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\FinancialReportService;

class DashboardController extends Controller
{
    public function index(
        Request $request,
        BalanceService $balanceService,
        FinancialReportService $reportService
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
                '!Y-m',
                $month
            );

            if ($selectedMonth->format('Y-m') !== $month) {
                throw new \Exception('Invalid month format');
            }
        } catch (\Exception $e) {

            $selectedMonth = now()->startOfMonth();

            $month = $selectedMonth->format('Y-m');
        }

        $selectedMonthLabel = $selectedMonth->format('F Y');

        /*
        |--------------------------------------------------------------------------
        | Accounts & Balances
        |--------------------------------------------------------------------------
        */

        $accounts = $user->accounts()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $accountBalances = [];

        foreach ($accounts as $account) {
            $accountBalances[$account->id] =
                $balanceService->getBalance($account);
        }

        $totalAssets = array_sum($accountBalances);


        /*
        |--------------------------------------------------------------------------
        | Current Month
        |--------------------------------------------------------------------------
        */

        $startOfMonth = $selectedMonth
            // ->format('F Y')
            ->copy()
            ->startOfMonth();

        $endOfMonth = $selectedMonth
            ->copy()
            ->endOfMonth();

        $report = $reportService->monthly(
            $user,
            $startOfMonth,
            $endOfMonth
        );

        $monthlyTransactions = $report['transactions'];

        $income = $report['income'];

        $expense = $report['expense'];

        $net = $report['net'];

        $expenseByCategory =
            $report['expenseByCategory'];


        /*
        |--------------------------------------------------------------------------
        | Income vs Expense Chart
        |--------------------------------------------------------------------------
        */

        $monthlyChart = collect();

        for ($i = 5; $i >= 0; $i--) {

            $chartMonth = $selectedMonth
                ->copy()
                ->subMonths($i);

            $start = $chartMonth->copy()
                ->startOfMonth();

            $end = $chartMonth->copy()
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
                'label' => $chartMonth->format('M Y'),

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
            ->whereBetween(
                'transaction_date',
                [
                    $startOfMonth,
                    $endOfMonth,
                ]
            )
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(10)
            ->get();


        return view(
            'dashboard',
            compact(
                'accounts',
                'accountBalances',
                // 'balances',
                'totalAssets',
                'income',
                'expense',
                'net',
                'recentTransactions',
                'monthlyChart',
                'expenseByCategory',
                'categories',
                'month',
                'selectedMonthLabel'
            )
        );
    }
}
