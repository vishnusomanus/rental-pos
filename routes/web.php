<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WhiteLabelController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect('/admin');
});

Auth::routes();

Route::prefix('admin')->middleware(['auth', 'restrict.login'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');

    Route::resource('products', ProductController::class);
    Route::get('/products/export/csv', [ProductController::class, 'exportCsv'])->name('products.export.csv');
    Route::get('/products/export/pdf', [ProductController::class, 'exportPdf'])->name('products.export.pdf');

    Route::resource('customers', CustomerController::class);
    Route::get('/customers/export/csv', [CustomerController::class, 'exportCsv'])->name('customers.export.csv');
    Route::get('/customers/export/pdf', [CustomerController::class, 'exportPdf'])->name('customers.export.pdf');

    Route::resource('orders', OrderController::class);
    Route::get('/orders/export/pdf', [OrderController::class, 'exportPDF'])->name('orders.export.pdf');
    Route::get('/orders/export/csv', [OrderController::class, 'exportCSV'])->name('orders.export.csv');

    Route::get('/pending', [OrderController::class, 'pending'])->name('orders.pending');
    Route::get('/orders/{id}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::post('/cart/change-qty', [CartController::class, 'changeQty']);
    Route::delete('/cart/delete', [CartController::class, 'delete']);
    Route::delete('/cart/empty', [CartController::class, 'empty']);

    Route::resource('users', UserController::class);
    Route::resource('white-labels', WhiteLabelController::class);

    // Transaltions route for React component
    Route::get('/locale/{type}', function ($type) {
        $translations = trans($type);
        return response()->json($translations);
    });
});
