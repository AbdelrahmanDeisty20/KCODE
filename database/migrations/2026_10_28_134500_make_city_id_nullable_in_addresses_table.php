<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * جعل city_id في جدول العناوين يقبل null.
     */
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropForeign(['city_id']);
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->unsignedBigInteger('city_id')->nullable()->change();
            $table->foreign('city_id')->references('id')->on('cities')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropForeign(['city_id']);
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->unsignedBigInteger('city_id')->nullable(false)->change();
            $table->foreign('city_id')->references('id')->on('cities')->cascadeOnDelete();
        });
    }
};
