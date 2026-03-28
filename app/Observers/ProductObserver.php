<?php

namespace App\Observers;

use App\Models\Product;
use App\Jobs\SyncProductToWooCommerce;

class ProductObserver
{
    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        // Check if stock_quantity changed
        if ($product->isDirty('stock_quantity')) {
            // Dispatch job to sync with WooCommerce
            SyncProductToWooCommerce::dispatch($product);
        }
    }

    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        // Sync new products to WooCommerce
        SyncProductToWooCommerce::dispatch($product);
    }
}