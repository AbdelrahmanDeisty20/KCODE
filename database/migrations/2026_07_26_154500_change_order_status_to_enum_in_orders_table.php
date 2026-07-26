<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Change order_status column to Enum.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN order_status ENUM('pending', 'accepted', 'cancelled', 'delivered') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN order_status VARCHAR(255) NOT NULL DEFAULT 'pending'");
    }
};
