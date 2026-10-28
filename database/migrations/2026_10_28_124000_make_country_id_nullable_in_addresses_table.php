<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * جعل country_id في العناوين يقبل null (الدولة اختيارية).
     */
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->nullable()->change();
            $table->foreign('country_id')->references('id')->on('countries')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->nullable(false)->change();
            $table->foreign('country_id')->references('id')->on('countries')->cascadeOnDelete();
        });
    }
};
