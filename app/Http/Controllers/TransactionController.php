<?php

namespace App\Http\Controllers;

use App\Models\User;
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
}
