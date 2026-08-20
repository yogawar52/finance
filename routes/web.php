<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReportController;

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

Route::resource('categories', CategoryController::class)
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

Route::patch(
    '/categories/{category}/toggle-status',
    [CategoryController::class, 'toggleStatus']
)->name('categories.toggle-status');

Route::get(
    '/reports/monthly',
    [ReportController::class, 'monthly']
)->name('reports.monthly');
