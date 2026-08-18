<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use Carbon\Carbon;

class BalanceService
{
    public function getBalance(Account $account): float
    {
        $income = Transaction::query()
            ->where('account_id', $account->id)
            ->where('type', 'income')
            ->sum('amount');

        $expense = Transaction::query()
            ->where('account_id', $account->id)
            ->where('type', 'expense')
            ->sum('amount');

        $transferOut = Transaction::query()
            ->where('account_id', $account->id)
            ->where('type', 'transfer')
            ->sum('amount');

        $transferIn = Transaction::query()
            ->where('destination_account_id', $account->id)
            ->where('type', 'transfer')
            ->sum('amount');

        return (float) (
            $income
            - $expense
            - $transferOut
            + $transferIn
        );
    }

    public function getBalanceAt(
        Account $account,
        Carbon $date
    ): float {
        $income = Transaction::query()
            ->where('account_id', $account->id)
            ->where('type', 'income')
            ->whereDate('transaction_date', '<=', $date)
            ->sum('amount');

        $expense = Transaction::query()
            ->where('account_id', $account->id)
            ->where('type', 'expense')
            ->whereDate('transaction_date', '<=', $date)
            ->sum('amount');

        $transferOut = Transaction::query()
            ->where('account_id', $account->id)
            ->where('type', 'transfer')
            ->whereDate('transaction_date', '<=', $date)
            ->sum('amount');

        $transferIn = Transaction::query()
            ->where('destination_account_id', $account->id)
            ->where('type', 'transfer')
            ->whereDate('transaction_date', '<=', $date)
            ->sum('amount');

        return (float) (
            $income
            - $expense
            - $transferOut
            + $transferIn
        );
    }
}
