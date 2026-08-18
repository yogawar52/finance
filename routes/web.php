<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

Route::resource('accounts', AccountController::class)
    ->only([
        'index',
        'create',
        'store',
        'edit',
        'update',
    ]);

Route::patch(
    '/accounts/{account}/toggle-status',
    [AccountController::class, 'toggleStatus']
)->name('accounts.toggle-status');

Route::get(
    '/transactions',
    [TransactionController::class, 'index']
)->name('transactions.index');

Route::get(
    '/transactions',
    [TransactionController::class, 'index']
)->name('transactions.index');

Route::get(
    '/transactions/create',
    [TransactionController::class, 'create']
)->name('transactions.create');

Route::post(
    '/transactions',
    [TransactionController::class, 'store']
)->name('transactions.store');

Route::get(
    '/transactions/{id}/edit',
    [TransactionController::class, 'edit']
)->name('transactions.edit');

Route::put(
    '/transactions/{id}',
    [TransactionController::class, 'update']
)->name('transactions.update');

Route::delete(
    '/transactions/{id}',
    [TransactionController::class, 'destroy']
)->name('transactions.destroy');
