<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\OrderCustomerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Order API Routes (Customer)
|--------------------------------------------------------------------------
*/
Route::prefix('orders')->group(function () {
    // Create new order
    Route::post('/', [OrderCustomerController::class, 'store']);

    // Get order details
    Route::get('/{orderId}', [OrderCustomerController::class, 'show']);

    // Check order status
    Route::get('/{orderId}/status', [OrderCustomerController::class, 'checkStatus']);

    // Cancel order (saat user close snap popup)
    Route::post('/{orderId}/cancel', [OrderCustomerController::class, 'cancel']);
});

/*
|--------------------------------------------------------------------------
| Payment Routes
|--------------------------------------------------------------------------
*/
// Payment Callback (dari Midtrans server)
Route::post('/payment/callback', [OrderCustomerController::class, 'paymentCallback']);

// Payment Finish (redirect setelah payment - bisa GET)
Route::get('/payment/finish', [OrderCustomerController::class, 'paymentFinish']);
