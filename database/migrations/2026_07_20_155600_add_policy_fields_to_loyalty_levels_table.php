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
        Schema::table('loyalty_levels', function (Blueprint $table) {
            if (!Schema::hasColumn('loyalty_levels', 'policy_ar')) {
                $table->text('policy_ar')->nullable()->after('description_en');
            }
            if (!Schema::hasColumn('loyalty_levels', 'policy_en')) {
                $table->text('policy_en')->nullable()->after('policy_ar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loyalty_levels', function (Blueprint $table) {
            if (Schema::hasColumn('loyalty_levels', 'policy_ar')) {
                $table->dropColumn('policy_ar');
            }
            if (Schema::hasColumn('loyalty_levels', 'policy_en')) {
                $table->dropColumn('policy_en');
            }
        });
    }
};
