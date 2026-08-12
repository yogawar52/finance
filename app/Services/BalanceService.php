<?php

namespace App\Services;

use App\Models\Account;
use Carbon\Carbon;

class BalanceService
{
    public function getBalance(Account $account): float
    {
        $balance = (float) $account->initial_balance;

        $balance += $account->transactions()
            ->where('type', 'income')
            ->sum('amount');

        $balance -= $account->transactions()
            ->where('type', 'expense')
            ->sum('amount');

        $balance -= $account->transactions()
            ->where('type', 'transfer')
            ->sum('amount');

        $balance += $account->transactions()
            ->where('type', 'adjustment')
            ->sum('amount');

        $balance += $account->destinationTransactions()
            ->where('type', 'transfer')
            ->sum('amount');

        return $balance;
    }

    public function getBalanceAt(
        Account $account,
        Carbon $date
    ): float {
        $balance = (float) $account->initial_balance;

        $balance += $account->transactions()
            ->where('type', 'income')
            ->whereDate('transaction_date', '<=', $date)
            ->sum('amount');

        $balance -= $account->transactions()
            ->where('type', 'expense')
            ->whereDate('transaction_date', '<=', $date)
            ->sum('amount');

        $balance -= $account->transactions()
            ->where('type', 'transfer')
            ->whereDate('transaction_date', '<=', $date)
            ->sum('amount');

        $balance += $account->transactions()
            ->where('type', 'adjustment')
            ->whereDate('transaction_date', '<=', $date)
            ->sum('amount');

        $balance += $account->destinationTransactions()
            ->where('type', 'transfer')
            ->whereDate('transaction_date', '<=', $date)
            ->sum('amount');

        return $balance;
    }
}
