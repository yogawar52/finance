<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\BalanceService;
use App\Services\FinancialReportService;

class ReportController extends Controller
{
    public function monthly(
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
            $date = Carbon::createFromFormat(
                'Y-m',
                $month
            );
        } catch (\Exception $e) {
            $date = now();
            $month = $date->format('Y-m');
        }

        $startDate = $date->copy()
            ->startOfMonth();

        $endDate = $date->copy()
            ->endOfMonth();

        $report = $reportService->monthly(
            $user,
            $startDate,
            $endDate
        );

        $transactions =
            $report['transactions'];

        $income =
            $report['income'];

        $expense =
            $report['expense'];

        $net =
            $report['net'];

        $incomeByCategory =
            $report['incomeByCategory'];

        $expenseByCategory =
            $report['expenseByCategory'];

        $categories = $user->categories()
            ->with('parent')
            ->get()
            ->keyBy('id');

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

        return view(
            'reports.monthly',
            compact(
                'month',
                'startDate',
                'endDate',
                'income',
                'expense',
                'net',
                'incomeByCategory',
                'expenseByCategory',
                'categories',
                'accounts',
                'accountBalances',
                'totalAssets'
            )
        );
    }
}
