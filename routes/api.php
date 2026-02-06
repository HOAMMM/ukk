<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\OrderCustomerController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
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

    // Get order detailsx
    Route::get('/{orderId}', [OrderCustomerController::class, 'show']);

    // Check order status
    Route::get('/{orderId}/status', [OrderCustomerController::class, 'checkStatus']);

    // Cancel order
    Route::post('/{orderId}/cancel', [OrderCustomerController::class, 'cancel']);
});

/*
|--------------------------------------------------------------------------
| Payment Callback (dari Payment Gateway)
|--------------------------------------------------------------------------
*/
Route::post('/payment/callback', [OrderCustomerController::class, 'paymentCallback']);
