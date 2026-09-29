<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| HALAMAN PUBLIK (CUSTOMER)
|--------------------------------------------------------------------------
*/

Route::prefix('customer')->name('customer.')->group(function () {
    Route::middleware('guest:customer')->group(function () {
        Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [CustomerAuthController::class, 'login'])->name('login.submit');
        Route::post('/register', [CustomerAuthController::class, 'register'])->name('register');
    });

    Route::middleware('auth:customer')->group(function () {
        Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');
        Route::get('/orders', [CustomerAuthController::class, 'orders'])->name('orders');
    });
});

Route::middleware('auth:customer')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::prefix('order')->name('order.')->group(function () {
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::post('/upload-proof', [OrderController::class, 'uploadProof'])->name('upload-proof');
        Route::get('/status', [OrderController::class, 'status'])->name('status');
    });
});

/*
|--------------------------------------------------------------------------
| ADMIN - AUTH
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    /*
    |----------------------------------------------------------------------
    | ADMIN - AREA TERPROTEKSI (butuh login)
    |----------------------------------------------------------------------
    */
    Route::middleware('admin.auth')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change-password');

        // Menu & kategori
        Route::get('/menu', [AdminMenuController::class, 'index'])->name('menu');
        Route::post('/menu/save', [AdminMenuController::class, 'save'])->name('menu.save');
        Route::post('/menu/{menu}/delete', [AdminMenuController::class, 'destroy'])->name('menu.destroy');
        Route::post('/menu/{menu}/toggle-availability', [AdminMenuController::class, 'toggleAvailability'])->name('menu.toggle');
        Route::post('/menu/{menu}/update-stock', [AdminMenuController::class, 'updateStock'])->name('menu.update-stock');
        Route::post('/menu/{menu}/adjust-stock', [AdminMenuController::class, 'adjustStock'])->name('menu.adjust-stock');
        Route::post('/category/save', [AdminMenuController::class, 'saveCategory'])->name('category.save');
        Route::post('/category/{category}/delete', [AdminMenuController::class, 'destroyCategory'])->name('category.destroy');

        // Pesanan
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders');
        Route::get('/orders/list', [AdminOrderController::class, 'list'])->name('orders.list');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/update-status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');

        // Pelanggan terdaftar
        Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers');

        // Pengaturan
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings');
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/toggle-open', [AdminSettingController::class, 'toggleOpen'])->name('settings.toggle-open');
        Route::post('/settings/upload-qris', [AdminSettingController::class, 'uploadQris'])->name('settings.upload-qris');

        // Laporan
        Route::get('/report', [AdminSettingController::class, 'reportPage'])->name('report');
        Route::get('/report/data', [AdminSettingController::class, 'report'])->name('report.data');
    });
});
