<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
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


Route::post('/webhooks/woocommerce/order-created', function (Request $request) {
    try {
        $order = $request->all();
        
        // Log the incoming webhook
        Log::info('WooCommerce Order Webhook Received', ['order_id' => $order['id'] ?? 'unknown']);
        
        // Dispatch job to process the order
        \App\Jobs\ProcessWooCommerceOrder::dispatch($order);
        
        return response()->json(['success' => true], 200);
        
    } catch (\Exception $e) {
        Log::error('WooCommerce Webhook Error: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
})->name('woocommerce.webhook.order');

