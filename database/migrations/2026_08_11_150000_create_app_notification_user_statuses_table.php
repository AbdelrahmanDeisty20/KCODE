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
        Schema::create('app_notification_user_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('user_fcm_token_id')->nullable()->constrained('user_fcm_tokens')->onDelete('cascade');
            $table->foreignId('app_notification_id')->constrained('app_notifications')->onDelete('cascade');
            $table->boolean('is_read')->default(false);
            $table->boolean('is_deleted')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'app_notification_id'], 'user_notification_status_unique');
            $table->unique(['user_fcm_token_id', 'app_notification_id'], 'fcm_token_notification_status_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_notification_user_statuses');
    }
};
