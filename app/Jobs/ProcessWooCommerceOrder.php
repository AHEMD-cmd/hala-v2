<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessWooCommerceOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $orderData;

    public function __construct(array $orderData)
    {
        $this->orderData = $orderData;
    }

    public function handle(): void
    {
        try {
            // Check if order status is valid for processing
            // $validStatuses = ['processing', 'completed', 'on-hold'];
            // $orderStatus = $this->orderData['status'] ?? null;
            
            // if (!in_array($orderStatus, $validStatuses)) {
            //     Log::info("Order status '{$orderStatus}' not valid for stock update");
            //     return;
            // }

            DB::transaction(function () {
                $lineItems = $this->orderData['line_items'] ?? [];
                
                foreach ($lineItems as $item) {
                    $sku = $item['sku'] ?? null;
                    $quantity = $item['quantity'] ?? 0;
                    
                    if (!$sku || $quantity <= 0) {
                        continue;
                    }
                    
                    // Find product by SKU in Laravel
                    $product = Product::where('sku', $sku)->first();
                    
                    if (!$product) {
                        Log::warning("Product with SKU '{$sku}' not found in Laravel dashboard");
                        continue;
                    }
                    
                    // Check if enough stock
                    if ($product->stock_quantity < $quantity) {
                        Log::error("Not enough stock for SKU '{$sku}'. Available: {$product->stock_quantity}, Requested: {$quantity}");
                        continue;
                    }
                    
                    $oldStock = $product->stock_quantity;
                    
                    // Decrease stock
                    $product->decrement('stock_quantity', $quantity);
                    
                    // Log the activity
                    activity('woocommerce')
                        ->performedOn($product)
                        ->withProperties([
                            'old_stock' => $oldStock,
                            'new_stock' => $product->stock_quantity,
                            'quantity_sold' => $quantity,
                            'woo_order_id' => $this->orderData['id'] ?? null,
                            'woo_order_number' => $this->orderData['number'] ?? null,
                            'product_name' => $product->name,
                            'sku' => $sku,
                        ])
                        ->log('Stock decreased due to WooCommerce order #' . ($this->orderData['number'] ?? $this->orderData['id']));
                    
                    Log::info("Stock updated for SKU '{$sku}': {$oldStock} → {$product->stock_quantity}");
                }
            });
            
            Log::info('WooCommerce order processed successfully', [
                'order_id' => $this->orderData['id'] ?? null,
                'order_number' => $this->orderData['number'] ?? null,
            ]);
            
        } catch (\Exception $e) {
            Log::error('ProcessWooCommerceOrder Error: ' . $e->getMessage(), [
                'order_id' => $this->orderData['id'] ?? null,
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessWooCommerceOrder Failed: ' . $exception->getMessage(), [
            'order_id' => $this->orderData['id'] ?? null,
        ]);
    }
}
