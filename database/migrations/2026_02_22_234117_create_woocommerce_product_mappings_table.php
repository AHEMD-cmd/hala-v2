<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('woocommerce_product_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('woocommerce_product_id');
            $table->timestamps();
        
            $table->unique(
                ['product_id', 'woocommerce_product_id'],
                'wpm_product_wc_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('woocommerce_product_mappings');
    }
};