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
use App\Http\Controllers\API\OrderCustomerController;
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
Route::get('/payment/finish', [OrderCustomerController::class, 'paymentFinish'])->name('payment.finish');

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
// Login dengan prevent.back
Route::middleware(['prevent.back'])->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('auth.login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware(['auth', 'prevent.back'])
    ->name('logout');

// Dashboard dengan prevent.back
Route::middleware(['auth', 'prevent.back'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // ... route lainnya

    // PENGATURAN
    Route::get('/dashboard/pengaturan', [App\Http\Controllers\PengaturanController::class, 'index'])
        ->name('pengaturan');
    Route::put('/dashboard/pengaturan/profile', [App\Http\Controllers\PengaturanController::class, 'updateProfile'])
        ->name('pengaturan.update.profile');
    Route::put('/dashboard/pengaturan/password', [App\Http\Controllers\PengaturanController::class, 'updatePassword'])
        ->name('pengaturan.update.password');
    Route::put('/dashboard/pengaturan/notifications', [App\Http\Controllers\PengaturanController::class, 'updateNotifications'])
        ->name('pengaturan.update.notifications');
    Route::put('/dashboard/pengaturan/preferences', [App\Http\Controllers\PengaturanController::class, 'updatePreferences'])
        ->name('pengaturan.update.preferences');




    /*
    |--------------------------------------------------------------------------
    | ADMIN ONLY
    |--------------------------------------------------------------------------
    */
    Route::middleware('level:1')->group(function () {



        // KATEGORI
        Route::resource('/dashboard/kategori', KategoriController::class)
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
        Route::put('/dashboard/staff-waiter/{id}', [StaffWaiterController::class, 'update'])->name('dashboard.waiter.update');
        Route::delete('/dashboard/staff-waiter/{id}', [StaffWaiterController::class, 'destroy'])->name('dashboard.waiter.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | ADMIN & WAITER
    |--------------------------------------------------------------------------
    */
    Route::middleware('level:1,2')->group(function () {

        Route::get('/dashboard/meja', [MejaController::class, 'index'])
            ->name('dashboard.meja.index');

        Route::post('/dashboard/meja', [MejaController::class, 'store'])
            ->name('dashboard.meja.store');

        Route::put('/dashboard/meja/{id}', [MejaController::class, 'update'])
            ->name('dashboard.meja.update');

        Route::delete('/dashboard/meja/{id}', [MejaController::class, 'destroy'])
            ->name('dashboard.meja.destroy');

        Route::patch('/dashboard/meja/{id}/toggle', [MejaController::class, 'toggle'])
            ->name('dashboard.meja.toggle');

        Route::post('/dashboard/meja/reset-all', [MejaController::class, 'resetAll'])
            ->name('dashboard.meja.resetAll');



        // Route untuk halaman pesanan
        Route::get('/dashboard/pesanan', [OrderController::class, 'indexwaiter'])->name('dashboard.pesanan');

        // Route untuk detail order (API endpoint untuk modal)
        Route::get('/dashboard/pesanan/{order_id}/detail', [OrderController::class, 'getOrderDetail']);

        // Route untuk hapus single order
        Route::delete('/dashboard/pesanan/{id}/hapus', [OrderController::class, 'hapuspesanan']);

        // Route untuk bulk operations
        Route::post('/dashboard/pesanan/bulk-delete', [OrderController::class, 'bulkDelete']);
        Route::post('/dashboard/pesanan/mark-paid', [OrderController::class, 'markAsPaid']);
        Route::post('/dashboard/pesanan/mark-pending', [OrderController::class, 'markAsPending']);



        // ✨ ROUTE BARU: Reset semua meja
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

    Route::middleware('level:1,4')->group(function () {
        // MENU
        Route::get('/dashboard/menu', [MenuController::class, 'index'])
            ->name('dashboard.menu');
        Route::post('/dashboard/menu', [MenuController::class, 'store'])
            ->name('dashboard.store.menu');
        Route::put('/dashboard/menu/{id}', [MenuController::class, 'update'])
            ->name('dashboard.update.menu');
        Route::delete('/dashboard/menu/{id}', [MenuController::class, 'destroy'])
            ->name('dashboard.menu.destroy');

        Route::get('/dashboard/laporan', [LaporanController::class, 'index'])
            ->name('dashboard.laporan');
        Route::get(
            '/dashboard/laporan/export-excel',
            [LaporanController::class, 'exportExcel']
        )->name('laporan.export.excel');
    });
});
