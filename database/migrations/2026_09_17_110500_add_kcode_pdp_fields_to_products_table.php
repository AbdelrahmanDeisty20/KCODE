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
        Schema::table('products', function (Blueprint $table) {
            $table->string('size')->nullable()->after('price');
            $table->string('barcode')->nullable()->after('sku');
            $table->boolean('sensitive_eligible')->default(false)->after('status');
            $table->text('brief_insight_ar')->nullable()->after('short_name_en');
            $table->string('country_of_origin_ar')->nullable()->after('barcode');
            $table->string('role_ar')->nullable()->after('why_kcode_en');
            $table->text('limitations_notes_ar')->nullable()->after('safety_notes_en');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'size',
                'barcode',
                'sensitive_eligible',
                'brief_insight_ar',
                'country_of_origin_ar',
                'role_ar',
                'limitations_notes_ar',
            ]);
        });
    }
};
