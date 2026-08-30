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
        Schema::table('preset_routines', function (Blueprint $table) {
            $table->boolean('is_for_men')->default(false)->after('goal_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preset_routines', function (Blueprint $table) {
            $table->dropColumn('is_for_men');
        });
    }
};
