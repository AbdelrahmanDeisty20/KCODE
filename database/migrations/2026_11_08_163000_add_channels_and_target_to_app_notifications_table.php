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
        Schema::table('app_notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('app_notifications', 'target_audience')) {
                $table->string('target_audience')->default('all_users')->after('type');
            }
            if (!Schema::hasColumn('app_notifications', 'channels')) {
                $table->json('channels')->nullable()->after('target_audience');
            }
            if (!Schema::hasColumn('app_notifications', 'action_url')) {
                $table->string('action_url')->nullable()->after('channels');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_notifications', function (Blueprint $table) {
            $table->dropColumn(['target_audience', 'channels', 'action_url']);
        });
    }
};

