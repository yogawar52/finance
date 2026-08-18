<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class TransactionService
{
    public function createIncome(
        User $user,
        Account $account,
        Category $category,
        float $amount,
        ?string $description = null,
        $date = null,
        ?array $metadata = null,
    ): Transaction {
        $this->validateAmount($amount);

        $this->validateAccount($user, $account);

        $this->validateCategory(
            $user,
            $category,
            'income'
        );

        return $user->transactions()->create([
            'type' => 'income',
            'account_id' => $account->id,
            'destination_account_id' => null,
            'category_id' => $category->id,
            'amount' => $amount,
            'description' => $description,
            'transaction_date' => $this->parseDate($date),
            'metadata' => $metadata,
        ]);
    }

    public function createExpense(
        User $user,
        Account $account,
        Category $category,
        float $amount,
        ?string $description = null,
        $date = null,
        ?array $metadata = null,
    ): Transaction {
        $this->validateAmount($amount);

        $this->validateAccount($user, $account);

        $this->validateCategory(
            $user,
            $category,
            'expense'
        );

        return $user->transactions()->create([
            'type' => 'expense',
            'account_id' => $account->id,
            'destination_account_id' => null,
            'category_id' => $category->id,
            'amount' => $amount,
            'description' => $description,
            'transaction_date' => $this->parseDate($date),
            'metadata' => $metadata,
        ]);
    }

    public function createTransfer(
        User $user,
        Account $sourceAccount,
        Account $destinationAccount,
        float $amount,
        ?string $description = null,
        $date = null,
        ?array $metadata = null,
    ): Transaction {
        $this->validateAmount($amount);

        $this->validateAccount(
            $user,
            $sourceAccount
        );

        $this->validateAccount(
            $user,
            $destinationAccount
        );

        if ($sourceAccount->id === $destinationAccount->id) {
            throw ValidationException::withMessages([
                'destination_account_id' =>
                    'Account sumber dan tujuan tidak boleh sama.',
            ]);
        }

        return $user->transactions()->create([
            'type' => 'transfer',
            'account_id' => $sourceAccount->id,
            'destination_account_id' => $destinationAccount->id,
            'category_id' => null,
            'amount' => $amount,
            'description' => $description,
            'transaction_date' => $this->parseDate($date),
            'metadata' => $metadata,
        ]);
    }

    protected function validateAmount(float $amount): void
    {
        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'Amount harus lebih besar dari 0.',
            ]);
        }
    }

    protected function validateAccount(
        User $user,
        Account $account
    ): void {
        if ($account->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'account_id' =>
                    'Account tidak valid.',
            ]);
        }

        if (!$account->is_active) {
            throw ValidationException::withMessages([
                'account_id' =>
                    'Account sudah tidak aktif.',
            ]);
        }
    }

    protected function validateCategory(
        User $user,
        Category $category,
        string $type
    ): void {
        if ($category->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'category_id' =>
                    'Category tidak valid.',
            ]);
        }

        if (!$category->is_active) {
            throw ValidationException::withMessages([
                'category_id' =>
                    'Category sudah tidak aktif.',
            ]);
        }

        if ($category->type !== $type) {
            throw ValidationException::withMessages([
                'category_id' =>
                    'Type category tidak sesuai dengan transaksi.',
            ]);
        }
    }

    protected function parseDate($date): string
    {
        if (!$date) {
            return now()->toDateString();
        }

        return Carbon::parse($date)->toDateString();
    }
}
