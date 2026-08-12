<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Transaction;
use App\Models\User;

class Account extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'initial_balance',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'initial_balance' => 'decimal:2',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // public function getBalanceAttribute()
    // {
    //     $balance = (float) $this->initial_balance;

    //     foreach ($this->transactions as $transaction) {
    //         if ($transaction->type === 'income') {
    //             $balance += (float) $transaction->amount;
    //         }

    //         if ($transaction->type === 'expense') {
    //             $balance -= (float) $transaction->amount;
    //         }

    //         if ($transaction->type === 'transfer') {
    //             $balance -= (float) $transaction->amount;
    //         }

    //         if ($transaction->type === 'adjustment') {
    //             $balance += (float) $transaction->amount;
    //         }
    //     }

    //     foreach ($this->destinationTransactions as $transaction) {
    //         if ($transaction->type === 'transfer') {
    //             $balance += (float) $transaction->amount;
    //         }
    //     }

    //     return $balance;
    // }

    public function destinationTransactions(): HasMany
    {
        return $this->hasMany(
            Transaction::class,
            'destination_account_id'
        );
    }

}
