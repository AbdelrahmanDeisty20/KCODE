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
        Schema::table('concerns', function (Blueprint $table) {
            if (!Schema::hasColumn('concerns', 'is_chosen')) {
                $table->boolean('is_chosen')->default(false)->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('concerns', function (Blueprint $table) {
            if (Schema::hasColumn('concerns', 'is_chosen')) {
                $table->dropColumn('is_chosen');
            }
        });
    }
};
