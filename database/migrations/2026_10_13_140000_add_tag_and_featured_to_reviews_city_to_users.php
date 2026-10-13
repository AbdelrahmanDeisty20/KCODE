<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add tag + featured to reviews
        Schema::table('reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('reviews', 'tag_ar')) {
                $table->string('tag_ar')->nullable()->after('comment');
            }
            if (!Schema::hasColumn('reviews', 'tag_en')) {
                $table->string('tag_en')->nullable()->after('tag_ar');
            }
            if (!Schema::hasColumn('reviews', 'featured')) {
                $table->boolean('featured')->default(false)->after('tag_en');
            }
        });

        // 2. Add city to users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable()->after('birth_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['tag_ar', 'tag_en', 'featured']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('city');
        });
    }
};
