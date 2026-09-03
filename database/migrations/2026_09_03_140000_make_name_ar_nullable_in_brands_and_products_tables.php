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
        Schema::table('brands', function (Blueprint $table) {
            $table->string('name_ar')->nullable()->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('name_ar')->nullable()->change();
            $table->string('short_name_ar')->nullable()->change();
            $table->text('description_ar')->nullable()->change();
            $table->text('ingredients_ar')->nullable()->change();
            $table->text('how_to_use_ar')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->string('name_ar')->nullable(false)->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('name_ar')->nullable(false)->change();
            $table->string('short_name_ar')->nullable(false)->change();
            $table->text('description_ar')->nullable(false)->change();
            $table->text('ingredients_ar')->nullable(false)->change();
            $table->text('how_to_use_ar')->nullable(false)->change();
        });
    }
};
