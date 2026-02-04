<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\MejaController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\Admin\StaffKasirController;
use App\Http\Controllers\Admin\StaffWaiterController;

/*
|--------------------------------------------------------------------------
| PUBLIC (CUSTOMER)
|--------------------------------------------------------------------------
*/

Route::get('/', [CustomerController::class, 'index']);
Route::post('/add-cart', [CustomerController::class, 'addCart']);
Route::get('/pesanan', [CustomerController::class, 'cart']);
Route::post('/checkout', [CustomerController::class, 'checkout']);

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'index'])->name('auth.login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| DASHBOARD (AUTH)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // PENGATURAN (accessible by all authenticated users)
    Route::view('/dashboard/pengaturan', 'dashboard.pengaturan');
    Route::get('/dashboard/laporan', [LaporanController::class, 'index'])
        ->name('dashboard.laporan');
    Route::get(
        '/dashboard/laporan/export-excel',
        [LaporanController::class, 'exportExcel']
    )->name('laporan.export.excel');



    /*
    |--------------------------------------------------------------------------
    | ADMIN ONLY
    |--------------------------------------------------------------------------
    */
    Route::middleware('level:1')->group(function () {

        // MENU
        Route::get('/dashboard/menu', [MenuController::class, 'index'])
            ->name('dashboard.menu');
        Route::post('/dashboard/menu', [MenuController::class, 'store'])
            ->name('dashboard.store.menu');
        Route::put('/dashboard/menu/{id}', [MenuController::class, 'update'])
            ->name('dashboard.update.menu');
        Route::delete('/dashboard/menu/{id}', [MenuController::class, 'destroy'])
            ->name('dashboard.menu.destroy');

        // KATEGORI
        Route::resource('/dashboard/kategori-produk', KategoriController::class)
            ->only(['index', 'store', 'update', 'destroy'])->names([
                'index'   => 'dashboard.kategori-produk',
                'store'   => 'dashboard.store.kategori',
                'update'  => 'dashboard.kategori.update',
                'destroy' => 'dashboard.kategori.destroy',
            ]);

        // STAFF
        Route::get('/dashboard/staff-kasir', [StaffKasirController::class, 'index'])->name('dashboard.staff-kasir');
        Route::post('/dashboard/staff-kasir', [StaffKasirController::class, 'store'])->name('dashboard.kasir.store');
        Route::put('/dashboard/staff-kasir/{id}', [StaffKasirController::class, 'update'])->name('dashboard.kasir.update');
        Route::delete('/dashboard/staff-kasir/{id}', [StaffKasirController::class, 'destroy'])->name('dashboard.kasir.destroy');

        Route::get('/dashboard/staff-waiter', [StaffWaiterController::class, 'index'])->name('dashboard.staff-waiter');
        Route::post('/dashboard/staff-waiter', [StaffWaiterController::class, 'store'])->name('dashboard.waiter.store');
        Route::delete('/dashboard/staff-waiter/{id}', [StaffWaiterController::class, 'destroy'])->name('dashboard.waiter.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | ADMIN & WAITER
    |--------------------------------------------------------------------------
    */
    Route::middleware('level:1,2')->group(function () {

        Route::get('/dashboard/meja', [MejaController::class, 'index']);
        Route::post('/dashboard/meja', [MejaController::class, 'store']);
        Route::put('/dashboard/meja/{id}', [MejaController::class, 'update']);
        Route::delete('/dashboard/meja/{id}', [MejaController::class, 'destroy']);
        Route::patch('/dashboard/meja/{id}/toggle', [MejaController::class, 'toggle']);
    });

    /*
    |--------------------------------------------------------------------------
    | ADMIN & KASIR
    |--------------------------------------------------------------------------
    */
    Route::middleware('level:3')->group(function () {



        Route::get('/dashboard/order', [OrderController::class, 'index'])->name('dashboard.order');
        Route::post('/dashboard/order/checkout', [OrderController::class, 'checkout'])->name('dashboard.order.checkout');
        Route::get('/dashboard/order/{id}/payment', [OrderController::class, 'payment'])
            ->name('dashboard.order.payment');
        Route::post(
            '/dashboard/order/payment/process',
            [OrderController::class, 'processPayment']
        )->name('dashboard.order.payment.process');
        Route::get(
            '/dashboard/order/struk/{order_id}',
            [OrderController::class, 'struk']
        )->name('dashboard.order.struk');
    });

    Route::middleware('level:1,3')->group(function () {

        // TRANSAKSI
        Route::get('/dashboard/transaksi', [TransaksiController::class, 'index'])
            ->name('dashboard.transaksi');
        Route::get('/dashboard/transaksi/{id}', [TransaksiController::class, 'show'])
            ->name('dashboard.transaksi.show');
        Route::delete('/dashboard/transaksi/{id}', [TransaksiController::class, 'destroy'])
            ->name('dashboard.transaksi.destroy');
        Route::patch('/dashboard/transaksi/{id}/status', [TransaksiController::class, 'updateStatus'])
            ->name('dashboard.transaksi.update.status');
    });
});
