<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class FinancialReportService
{
    public function monthly(
        User $user,
        Carbon $startDate,
        Carbon $endDate
    ): array {

        $transactions = $user->transactions()
            ->with([
                'account',
                'destinationAccount',
                'category',
                'category.parent',
            ])
            ->whereBetween(
                'transaction_date',
                [
                    $startDate,
                    $endDate,
                ]
            )
            ->get();

        $income = $transactions
            ->where('type', 'income')
            ->sum('amount');

        $expense = $transactions
            ->where('type', 'expense')
            ->sum('amount');

        $net = $income - $expense;

        $incomeByCategory = $transactions
            ->where('type', 'income')
            ->groupBy('category_id')
            ->map(function ($items) {
                return $items->sum('amount');
            });

        $expenseByCategory = $transactions
            ->where('type', 'expense')
            ->groupBy('category_id')
            ->map(function ($items) {
                return $items->sum('amount');
            });

        return [
            'transactions' => $transactions,

            'income' => (float) $income,

            'expense' => (float) $expense,

            'net' => (float) $net,

            'incomeByCategory' => $incomeByCategory,

            'expenseByCategory' => $expenseByCategory,
        ];
    }
}
