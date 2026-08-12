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
        Schema::table('app_notification_user_statuses', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();

            if (!Schema::hasColumn('app_notification_user_statuses', 'user_fcm_token_id')) {
                $table->foreignId('user_fcm_token_id')->nullable()->after('user_id')->constrained('user_fcm_tokens')->onDelete('cascade');
                $table->unique(['user_fcm_token_id', 'app_notification_id'], 'fcm_token_notification_status_unique');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_notification_user_statuses', function (Blueprint $table) {
            if (Schema::hasColumn('app_notification_user_statuses', 'user_fcm_token_id')) {
                $table->dropUnique('fcm_token_notification_status_unique');
                $table->dropForeign(['user_fcm_token_id']);
                $table->dropColumn('user_fcm_token_id');
            }
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};
