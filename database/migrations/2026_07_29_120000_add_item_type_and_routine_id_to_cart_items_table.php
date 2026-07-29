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
        Schema::table('cart_items', function (Blueprint $table) {
            if (!Schema::hasColumn('cart_items', 'item_type')) {
                $table->enum('item_type', ['single', 'routine'])->default('single')->after('product_id');
            }
            if (!Schema::hasColumn('cart_items', 'routine_id')) {
                $table->foreignId('routine_id')->nullable()->after('item_type')->constrained('routines')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'routine_id')) {
                $table->dropForeign(['routine_id']);
                $table->dropColumn('routine_id');
            }
            if (Schema::hasColumn('cart_items', 'item_type')) {
                $table->dropColumn('item_type');
            }
        });
    }
};
