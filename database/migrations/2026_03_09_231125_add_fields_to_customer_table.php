<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('post_code')->nullable();
            $table->string('city')->nullable();
            $table->string('LEVERING')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->dropColumn('post_code');
            $table->dropColumn('city');
            $table->dropColumn('LEVERING');
        });
    }
};
