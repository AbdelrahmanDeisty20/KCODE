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
        if (!Schema::hasColumn('carts', 'user_id')) {
            Schema::table('carts', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->cascadeOnDelete();
            });
        }

        if (!Schema::hasColumn('carts', 'session_id')) {
            Schema::table('carts', function (Blueprint $table) {
                $table->string('session_id')->nullable()->after('user_id');
            });
        }

        Schema::table('cart_items', function (Blueprint $table) {
            if (!Schema::hasColumn('cart_items', 'cart_id')) {
                $table->foreignId('cart_id')->after('id')->constrained('carts')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('cart_items', 'product_id')) {
                $table->foreignId('product_id')->after('cart_id')->constrained('products')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('cart_items', 'quantity')) {
                $table->integer('quantity')->default(1)->after('product_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'cart_id')) {
                $table->dropForeign(['cart_id']);
                $table->dropColumn(['cart_id']);
            }
            if (Schema::hasColumn('cart_items', 'product_id')) {
                $table->dropForeign(['product_id']);
                $table->dropColumn(['product_id']);
            }
            if (Schema::hasColumn('cart_items', 'quantity')) {
                $table->dropColumn(['quantity']);
            }
        });

        Schema::table('carts', function (Blueprint $table) {
            if (Schema::hasColumn('carts', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn(['user_id']);
            }
            if (Schema::hasColumn('carts', 'session_id')) {
                $table->dropColumn(['session_id']);
            }
        });
    }
};
