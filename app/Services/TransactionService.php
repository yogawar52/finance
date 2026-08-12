<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class TransactionService
{
    public function createExpense(
        User $user,
        Account $account,
        Category $category,
        float $amount,
        string $description,
        string $transactionDate,
        ?array $metadata = null,
    ): Transaction {
        $this->validateAccount($user, $account);
        $this->validateCategory($user, $category, 'expense');
        $this->validateAmount($amount);

        return $user->transactions()->create([
            'type' => 'expense',
            'account_id' => $account->id,
            'destination_account_id' => null,
            'category_id' => $category->id,
            'amount' => $amount,
            'description' => $description,
            'transaction_date' => $transactionDate,
            'metadata' => $metadata,
        ]);
    }

    public function createIncome(
        User $user,
        Account $account,
        Category $category,
        float $amount,
        string $description,
        string $transactionDate,
        ?array $metadata = null,
    ): Transaction {
        $this->validateAccount($user, $account);
        $this->validateCategory($user, $category, 'income');
        $this->validateAmount($amount);

        return $user->transactions()->create([
            'type' => 'income',
            'account_id' => $account->id,
            'destination_account_id' => null,
            'category_id' => $category->id,
            'amount' => $amount,
            'description' => $description,
            'transaction_date' => $transactionDate,
            'metadata' => $metadata,
        ]);
    }

    public function createTransfer(
        User $user,
        Account $sourceAccount,
        Account $destinationAccount,
        float $amount,
        string $description,
        string $transactionDate,
        ?array $metadata = null,
    ): Transaction {
        $this->validateAccount($user, $sourceAccount);
        $this->validateAccount($user, $destinationAccount);
        $this->validateAmount($amount);

        if ($sourceAccount->id === $destinationAccount->id) {
            throw ValidationException::withMessages([
                'destination_account' => 'Source dan destination account tidak boleh sama.',
            ]);
        }

        return $user->transactions()->create([
            'type' => 'transfer',
            'account_id' => $sourceAccount->id,
            'destination_account_id' => $destinationAccount->id,
            'category_id' => null,
            'amount' => $amount,
            'description' => $description,
            'transaction_date' => $transactionDate,
            'metadata' => $metadata,
        ]);
    }

    protected function validateAccount(
        User $user,
        Account $account
    ): void {
        if ($account->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'account' => 'Account bukan milik user ini.',
            ]);
        }

        if (!$account->is_active) {
            throw ValidationException::withMessages([
                'account' => 'Account sudah tidak aktif.',
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
                'category' => 'Category bukan milik user ini.',
            ]);
        }

        if (!$category->is_active) {
            throw ValidationException::withMessages([
                'category' => 'Category sudah tidak aktif.',
            ]);
        }

        if ($category->type !== $type) {
            throw ValidationException::withMessages([
                'category' => "Category harus bertipe {$type}.",
            ]);
        }
    }

    protected function validateAmount(float $amount): void
    {
        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'Amount harus lebih besar dari 0.',
            ]);
        }
    }
}
