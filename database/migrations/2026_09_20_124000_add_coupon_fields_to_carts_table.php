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
        Schema::table('carts', function (Blueprint $table) {
            if (!Schema::hasColumn('carts', 'coupon_code')) {
                $table->string('coupon_code')->nullable()->after('session_id');
            }
            if (!Schema::hasColumn('carts', 'coupon_discount')) {
                $table->decimal('coupon_discount', 10, 3)->default(0.000)->after('coupon_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            if (Schema::hasColumn('carts', 'coupon_code')) {
                $table->dropColumn('coupon_code');
            }
            if (Schema::hasColumn('carts', 'coupon_discount')) {
                $table->dropColumn('coupon_discount');
            }
        });
    }
};
