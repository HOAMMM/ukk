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

    // Payment Cash (Tunai)
    Route::post('/payment/cash', [OrderCustomerController::class, 'paymentCash']);

    // Cancel Payment
    Route::post('/payment/cancel', [OrderCustomerController::class, 'cancelPayment']);
});

/*
|--------------------------------------------------------------------------
| Payment Callback (dari Payment Gateway)
|--------------------------------------------------------------------------
*/
Route::post('/payment/callback', [OrderCustomerController::class, 'paymentCallback']);
