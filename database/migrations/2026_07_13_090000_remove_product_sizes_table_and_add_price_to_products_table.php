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
        // 1. Drop product_sizes table
        Schema::dropIfExists('product_sizes');

        // 2. Add price and stock columns to products table
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->after('brand_id')->default(0.00);
            $table->integer('stock')->after('price')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Remove price and stock columns from products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['price', 'stock']);
        });

        // 2. Re-create product_sizes table
        Schema::create('product_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('size_ar');
            $table->string('size_en');
            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(1);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }
};
