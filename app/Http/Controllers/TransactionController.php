<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $user = User::where(
            'email',
            'yoga@example.com'
        )->firstOrFail();

        $transactions = $user->transactions()
            ->with([
                'account',
                'destinationAccount',
                'category',
            ])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->get();

        return view(
            'transactions.index',
            compact('transactions')
        );
    }

    public function create()
    {
        $user = User::where(
            'email',
            'yoga@example.com'
        )->firstOrFail();

        $accounts = $user->accounts()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $categories = $user->categories()
            ->with('parent')
            ->where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view(
            'transactions.create',
            compact(
                'accounts',
                'categories'
            )
        );
    }

    public function store(
        Request $request,
        TransactionService $transactionService
    ) {
        $validated = $request->validate([
            'type' => [
                'required',
                'in:income,expense,transfer',
            ],

            'account_id' => [
                'required',
                'integer',
            ],

            'destination_account_id' => [
                'nullable',
                'integer',
            ],

            'category_id' => [
                'nullable',
                'integer',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'description' => [
                'nullable',
                'string',
                'max:255',
            ],

            'transaction_date' => [
                'required',
                'date',
            ],
        ]);

        $user = User::where(
            'email',
            'yoga@example.com'
        )->firstOrFail();

        $account = $user->accounts()
            ->findOrFail(
                $validated['account_id']
            );

        $category = null;

        if (
            !empty($validated['category_id'])
        ) {
            $category = $user->categories()
                ->findOrFail(
                    $validated['category_id']
                );
        }

        if (
            $validated['type'] === 'income'
        ) {
            if (!$category) {
                return back()
                    ->withErrors([
                        'category_id' =>
                        'Category wajib dipilih untuk income.',
                    ])
                    ->withInput();
            }

            $transactionService->createIncome(
                $user,
                $account,
                $category,
                (float) $validated['amount'],
                $validated['description'] ?? null,
                $validated['transaction_date']
            );
        } elseif (
            $validated['type'] === 'expense'
        ) {
            if (!$category) {
                return back()
                    ->withErrors([
                        'category_id' =>
                        'Category wajib dipilih untuk expense.',
                    ])
                    ->withInput();
            }

            $transactionService->createExpense(
                $user,
                $account,
                $category,
                (float) $validated['amount'],
                $validated['description'] ?? null,
                $validated['transaction_date']
            );
        } else {
            if (
                empty($validated['destination_account_id'])
            ) {
                return back()
                    ->withErrors([
                        'destination_account_id' =>
                        'Account tujuan wajib dipilih untuk transfer.',
                    ])
                    ->withInput();
            }

            $destinationAccount =
                $user->accounts()->findOrFail(
                    $validated['destination_account_id']
                );

            $transactionService->createTransfer(
                $user,
                $account,
                $destinationAccount,
                (float) $validated['amount'],
                $validated['description'] ?? null,
                $validated['transaction_date']
            );
        }

        return redirect()
            ->route('transactions.index')
            ->with(
                'success',
                'Transaction berhasil ditambahkan.'
            );
    }

    public function edit(int $id)
    {
        $user = User::where(
            'email',
            'yoga@example.com'
        )->firstOrFail();

        $transaction = $user->transactions()
            ->with([
                'account',
                'destinationAccount',
                'category',
            ])
            ->findOrFail($id);

        $accounts = $user->accounts()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $categories = $user->categories()
            ->with('parent')
            ->where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view(
            'transactions.edit',
            compact(
                'transaction',
                'accounts',
                'categories'
            )
        );
    }

    public function update(
        Request $request,
        int $id,
        TransactionService $transactionService
    ) {
        $user = User::where(
            'email',
            'yoga@example.com'
        )->firstOrFail();

        $transaction = $user->transactions()
            ->findOrFail($id);

        $validated = $request->validate([
            'type' => [
                'required',
                'in:income,expense,transfer',
            ],

            'account_id' => [
                'required',
                'integer',
            ],

            'destination_account_id' => [
                'nullable',
                'integer',
            ],

            'category_id' => [
                'nullable',
                'integer',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'description' => [
                'nullable',
                'string',
                'max:255',
            ],

            'transaction_date' => [
                'required',
                'date',
            ],
        ]);

        /*
    |--------------------------------------------------------------------------
    | Untuk sementara kita hapus transaksi lama,
    | kemudian buat transaksi baru melalui Service.
    |--------------------------------------------------------------------------
    */

        $transaction->delete();

        $account = $user->accounts()
            ->findOrFail(
                $validated['account_id']
            );

        $category = null;

        if (!empty($validated['category_id'])) {
            $category = $user->categories()
                ->findOrFail(
                    $validated['category_id']
                );
        }

        if ($validated['type'] === 'income') {

            if (!$category) {
                return back()
                    ->withErrors([
                        'category_id' =>
                        'Category wajib dipilih untuk income.',
                    ])
                    ->withInput();
            }

            $transactionService->createIncome(
                $user,
                $account,
                $category,
                (float) $validated['amount'],
                $validated['description'] ?? null,
                $validated['transaction_date']
            );
        } elseif ($validated['type'] === 'expense') {

            if (!$category) {
                return back()
                    ->withErrors([
                        'category_id' =>
                        'Category wajib dipilih untuk expense.',
                    ])
                    ->withInput();
            }

            $transactionService->createExpense(
                $user,
                $account,
                $category,
                (float) $validated['amount'],
                $validated['description'] ?? null,
                $validated['transaction_date']
            );
        } else {

            if (
                empty($validated['destination_account_id'])
            ) {
                return back()
                    ->withErrors([
                        'destination_account_id' =>
                        'Account tujuan wajib dipilih untuk transfer.',
                    ])
                    ->withInput();
            }

            $destinationAccount =
                $user->accounts()->findOrFail(
                    $validated['destination_account_id']
                );

            $transactionService->createTransfer(
                $user,
                $account,
                $destinationAccount,
                (float) $validated['amount'],
                $validated['description'] ?? null,
                $validated['transaction_date']
            );
        }

        return redirect()
            ->route('transactions.index')
            ->with(
                'success',
                'Transaction berhasil diperbarui.'
            );
    }

    public function destroy(int $id)
    {
        $user = User::where(
            'email',
            'yoga@example.com'
        )->firstOrFail();

        $transaction = $user->transactions()
            ->findOrFail($id);

        $transaction->delete();

        return redirect()
            ->route('transactions.index')
            ->with(
                'success',
                'Transaction berhasil dihapus.'
            );
    }
}
