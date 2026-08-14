<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $user = User::where('email', 'yoga@example.com')->firstOrFail();

        $accounts = $user->accounts()
            ->orderBy('name')
            ->get();

        return view('accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('accounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'string', 'max:50'],
            'initial_balance' => ['required', 'numeric', 'min:0'],
        ]);

        $user = User::where('email', 'yoga@example.com')->firstOrFail();

        $user->accounts()->create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'initial_balance' => $validated['initial_balance'],
            'is_active' => true,
            'is_default' => false,
        ]);

        return redirect()
            ->route('accounts.index')
            ->with('success', 'Account berhasil dibuat.');
    }

    public function edit(int $account)
    {
        $user = User::where('email', 'yoga@example.com')->firstOrFail();

        $account = $user->accounts()->findOrFail($account);

        return view('accounts.edit', compact('account'));
    }

    public function update(Request $request, int $account)
    {
        $user = User::where('email', 'yoga@example.com')->firstOrFail();

        $account = $user->accounts()->findOrFail($account);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'string', 'max:50'],
            'initial_balance' => ['required', 'numeric', 'min:0'],
        ]);

        $account->update($validated);

        return redirect()
            ->route('accounts.index')
            ->with('success', 'Account berhasil diperbarui.');
    }

    public function toggleStatus(int $account)
    {
        $user = User::where('email', 'yoga@example.com')->firstOrFail();

        $account = $user->accounts()->findOrFail($account);

        $account->update([
            'is_active' => ! $account->is_active,
        ]);

        return redirect()
            ->route('accounts.index')
            ->with(
                'success',
                $account->is_active
                    ? 'Account berhasil diaktifkan.'
                    : 'Account berhasil dinonaktifkan.'
            );
    }
}
