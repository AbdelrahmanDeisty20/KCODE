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
        Schema::create('blog_seo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->unique()->constrained('blogs')->cascadeOnDelete();
            $table->string('meta_title_ar')->nullable();
            $table->string('meta_title_en')->nullable();
            $table->text('meta_description_ar')->nullable();
            $table->text('meta_description_en')->nullable();
            $table->text('meta_keywords_ar')->nullable();
            $table->text('meta_keywords_en')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_title_ar')->nullable();
            $table->string('og_title_en')->nullable();
            $table->text('og_description_ar')->nullable();
            $table->text('og_description_en')->nullable();
            $table->string('og_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_seo');
    }
};
