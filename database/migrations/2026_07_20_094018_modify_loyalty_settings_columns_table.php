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
        Schema::table('loyalty_settings', function (Blueprint $table) {
            $table->dropColumn(['name_ar', 'name_en', 'description_ar', 'description_en', 'value']);
            $table->string('key_ar')->after('key');
            $table->string('key_en')->after('key_ar');
            $table->string('value_ar')->nullable()->after('key_en');
            $table->string('value_en')->nullable()->after('value_ar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loyalty_settings', function (Blueprint $table) {
            $table->dropColumn(['key_ar', 'key_en', 'value_ar', 'value_en']);
            $table->string('value')->after('key');
            $table->string('name_ar')->after('value');
            $table->string('name_en')->after('name_ar');
            $table->text('description_ar')->nullable()->after('name_en');
            $table->text('description_en')->nullable()->after('description_ar');
        });
    }
};
