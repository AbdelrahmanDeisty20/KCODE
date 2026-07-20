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
        Schema::create('loyalty_points_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('points'); // positive for earning, negative for redemption
            $table->string('source_type'); // e.g., purchase, signup, review, referral, adjustment, redemption
            $table->unsignedBigInteger('source_id')->nullable(); // e.g., order_id, review_id
            $table->string('description_ar');
            $table->string('description_en');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_points_ledger');
    }
};
