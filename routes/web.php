<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::redirect('/', '/accounting');

    Route::get('/accounting', [AccountingController::class, 'dashboard'])->name('accounting.dashboard');
    Route::get('/accounting/accounts/{account}', [AccountingController::class, 'ledger'])->name('accounting.ledger');

    Route::get('/settings', [SettingsController::class, 'show'])->name('settings.show');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::resource('customers', CustomerController::class)->except('show');
    Route::resource('products', ProductController::class)->except('show');

    Route::resource('orders', OrderController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
    Route::post('/orders/{order}/complete', [OrderController::class, 'complete'])->name('orders.complete');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
});
