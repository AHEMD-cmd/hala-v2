<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->string('employee_name');
            $table->decimal('amount', 10, 2);
            $table->date('month'); // أول يوم في الشهر
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};