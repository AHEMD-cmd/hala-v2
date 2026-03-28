<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update the enum to include 'archived'
        DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('active', 'pending_delete', 'deleted', 'archived') DEFAULT 'active'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('active', 'pending_delete', 'deleted') DEFAULT 'active'");
    }
};