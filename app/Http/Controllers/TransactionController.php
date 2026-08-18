<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = User::where(
            'email',
            'yoga@example.com'
        )->firstOrFail();

        $accounts = $user->accounts()
            ->orderBy('name')
            ->get();

        $categories = $user->categories()
            ->with('parent')
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $query = $user->transactions()
            ->with([
                'account',
                'destinationAccount',
                'category',
            ]);

        // Search description
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(
                'description',
                'like',
                '%' . $search . '%'
            );
        }

        // Filter type
        if ($request->filled('type')) {
            $query->where(
                'type',
                $request->input('type')
            );
        }

        // Filter account
        if ($request->filled('account_id')) {

            $accountId = $request->input('account_id');

            $query->where(function ($q) use ($accountId) {

                $q->where(
                    'account_id',
                    $accountId
                )->orWhere(
                    'destination_account_id',
                    $accountId
                );
            });
        }

        // Filter category
        if ($request->filled('category_id')) {

            $query->where(
                'category_id',
                $request->input('category_id')
            );
        }

        // Filter start date
        if ($request->filled('date_from')) {

            $query->whereDate(
                'transaction_date',
                '>=',
                $request->input('date_from')
            );
        }

        // Filter end date
        if ($request->filled('date_to')) {

            $query->whereDate(
                'transaction_date',
                '<=',
                $request->input('date_to')
            );
        }

        $transactions = $query
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->get();

        return view(
            'transactions.index',
            compact(
                'transactions',
                'accounts',
                'categories'
            )
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

        $transaction = $user->transactions()
            ->findOrFail($id);

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

        $destinationAccount = null;

        if (
            !empty($validated['destination_account_id'])
        ) {
            $destinationAccount = $user->accounts()
                ->findOrFail(
                    $validated['destination_account_id']
                );
        }

        $transactionService->updateTransaction(
            $user,
            $transaction,
            $account,
            $destinationAccount,
            $category,
            $validated['type'],
            (float) $validated['amount'],
            $validated['description'] ?? null,
            $validated['transaction_date']
        );

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
