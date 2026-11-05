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
            $table->string('slug')->nullable()->after('id')->index();
            $table->string('subtitle_ar')->nullable()->after('title_en');
            $table->string('subtitle_en')->nullable()->after('subtitle_ar');
            $table->string('card_note_ar')->nullable()->after('description_en');
            $table->string('card_note_en')->nullable()->after('card_note_ar');
            $table->text('audience_ar')->nullable()->after('card_note_en');
            $table->text('audience_en')->nullable()->after('audience_ar');
            $table->json('timing_json')->nullable()->after('audience_en');
            $table->string('cover_image')->nullable()->after('timing_json');
            $table->json('cover_json')->nullable()->after('cover_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preset_routines', function (Blueprint $table) {
            $table->dropColumn([
                'slug',
                'subtitle_ar',
                'subtitle_en',
                'card_note_ar',
                'card_note_en',
                'audience_ar',
                'audience_en',
                'timing_json',
                'cover_image',
                'cover_json',
            ]);
        });
    }
};

