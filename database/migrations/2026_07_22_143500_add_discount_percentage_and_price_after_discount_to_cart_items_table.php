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
            if (!Schema::hasColumn('cart_items', 'discount_percentage')) {
                $table->decimal('discount_percentage', 5, 2)->default(0)->after('discount_amount');
            }
            if (!Schema::hasColumn('cart_items', 'price_after_discount')) {
                $table->decimal('price_after_discount', 10, 2)->nullable()->after('discount_percentage');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'discount_percentage')) {
                $table->dropColumn('discount_percentage');
            }
            if (Schema::hasColumn('cart_items', 'price_after_discount')) {
                $table->dropColumn('price_after_discount');
            }
        });
    }
};
