<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoryController;

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
    ]);

Route::patch(
    '/accounts/{account}/toggle-status',
    [AccountController::class, 'toggleStatus']
)->name('accounts.toggle-status');
