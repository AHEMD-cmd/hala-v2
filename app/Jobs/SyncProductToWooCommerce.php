<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\WooCommerceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncProductToWooCommerce implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Product $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function handle(WooCommerceService $woocommerce): void
    {
        // Find product in WooCommerce by SKU
        $wooProduct = $woocommerce->findProductBySku($this->product->sku);

        if (!$wooProduct) {
            Log::warning("Product with SKU {$this->product->sku} not found in WooCommerce");
            return;
        }

        // Update stock status based on quantity
        $inStock = $this->product->stock_quantity > 0;
        $woocommerce->updateStockQuantity($wooProduct->id, $this->product->stock_quantity);

        // Log the activity
        activity('woocommerce')
            ->performedOn($this->product)
            ->withProperties([
                'woo_product_id' => $wooProduct->id,
                'stock_status' => $inStock ? 'instock' : 'outofstock',
                'stock_quantity' => $this->product->stock_quantity,
            ])
            ->log('Product synced to WooCommerce');
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('WooCommerce Sync Failed: ' . $exception->getMessage());
    }
}