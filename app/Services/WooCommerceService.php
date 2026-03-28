<?php

namespace App\Services;

use Automattic\WooCommerce\Client;
use Illuminate\Support\Facades\Log;

class WooCommerceService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client(
            config('services.woocommerce.url'),
            config('services.woocommerce.consumer_key'),
            config('services.woocommerce.consumer_secret'),
            [
                'version' => config('services.woocommerce.version'),
                'verify_ssl' => true,
                'timeout' => 30,
            ]
        );
    }

    /**
     * Find WooCommerce product by SKU
     */
    public function findProductBySku(string $sku)
    {
        try {
            $response = $this->client->get('products', [
                'sku' => $sku,
                'per_page' => 1,
            ]);

            return !empty($response) ? $response[0] : null;
        } catch (\Exception $e) {
            Log::error('WooCommerce Find Product Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update product stock status in WooCommerce
     */
    public function updateStockQuantity(int $productId, int $quantity): bool
    {
        try {
            $data = [
                'stock_quantity' => (int) $quantity,
                'manage_stock' => true, 
                'stock_status' => $quantity > 0 ? 'instock' : 'outofstock',
            ];

            $response = $this->client->put("products/{$productId}", $data);

            Log::info("WooCommerce API Response", [
                'response' => json_encode($response),
            ]);

            Log::info("WooCommerce product {$productId} stock updated to {$quantity}");

            return true;
        } catch (\Exception $e) {
            Log::error('WooCommerce Update Stock Quantity Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update product stock status only
     */
    public function updateStockStatus(int $productId, bool $inStock): bool
    {
        try {
            $data = [
                'stock_status' => $inStock ? 'instock' : 'outofstock',
                'manage_stock' => true, // Changed to true
            ];

            $this->client->put("products/{$productId}", $data);

            Log::info("WooCommerce product {$productId} updated to " . ($inStock ? 'in stock' : 'out of stock'));

            return true;
        } catch (\Exception $e) {
            Log::error('WooCommerce Update Stock Error: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Get product details from WooCommerce
     */
    public function getProduct(int $productId)
    {
        try {
            return $this->client->get("products/{$productId}");
        } catch (\Exception $e) {
            Log::error('WooCommerce Get Product Error: ' . $e->getMessage());
            return null;
        }
    }
}
