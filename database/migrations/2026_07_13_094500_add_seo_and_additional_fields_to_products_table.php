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
        Schema::table('products', function (Blueprint $table) {
            // Product details & Sub-category
            $table->foreignId('sub_category_id')->nullable()->after('category_id')->constrained('sub_categories')->nullOnDelete();
            $table->string('texture_ar')->nullable()->after('ingredients_en');
            $table->string('texture_en')->nullable()->after('texture_ar');
            $table->text('why_kcode_ar')->nullable()->after('texture_en');
            $table->text('why_kcode_en')->nullable()->after('why_kcode_ar');
            $table->string('usage_frequency_ar')->nullable()->after('why_kcode_en');
            $table->enum('active_strength_level', ['Low', 'Medium', 'High'])->nullable()->after('usage_frequency_ar');
            $table->text('safety_notes_ar')->nullable()->after('active_strength_level');
            $table->text('safety_notes_en')->nullable()->after('safety_notes_ar');
            $table->text('ar_key_benefits')->nullable()->after('safety_notes_en');
            $table->text('en_key_benefits')->nullable()->after('ar_key_benefits');

            // SEO Columns
            $table->string('ar_product_title_seo')->nullable()->after('en_key_benefits');
            $table->string('en_product_title_seo')->nullable()->after('ar_product_title_seo');
            $table->string('en_short_hook')->nullable()->after('en_product_title_seo');
            $table->string('seo_meta_title_ar')->nullable()->after('en_short_hook');
            $table->text('meta_description_en')->nullable()->after('seo_meta_title_ar');
            $table->text('meta_description_ar')->nullable()->after('meta_description_en');
            $table->string('primary_keyword_en')->nullable()->after('meta_description_ar');
            $table->string('primary_keyword_ar')->nullable()->after('primary_keyword_en');
            $table->text('secondary_keywords_en')->nullable()->after('primary_keyword_ar');
            $table->text('secondary_keywords_ar')->nullable()->after('secondary_keywords_en');
            $table->string('final_url_slug')->nullable()->after('secondary_keywords_ar');
            $table->string('image_alt_en')->nullable()->after('final_url_slug');
            $table->string('image_alt_ar')->nullable()->after('image_alt_en');
            $table->string('og_title_ar')->nullable()->after('image_alt_ar');
            $table->text('og_description_en')->nullable()->after('og_title_ar');
            $table->text('og_description_ar')->nullable()->after('og_description_en');
            $table->string('pdp_headline_en')->nullable()->after('og_description_ar');
            $table->string('above_fold_hook_en')->nullable()->after('pdp_headline_en');
            $table->text('keywords')->nullable()->after('above_fold_hook_en');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['sub_category_id']);
            $table->dropColumn([
                'sub_category_id',
                'texture_ar',
                'texture_en',
                'why_kcode_ar',
                'why_kcode_en',
                'usage_frequency_ar',
                'active_strength_level',
                'safety_notes_ar',
                'safety_notes_en',
                'ar_key_benefits',
                'en_key_benefits',
                'ar_product_title_seo',
                'en_product_title_seo',
                'en_short_hook',
                'seo_meta_title_ar',
                'meta_description_en',
                'meta_description_ar',
                'primary_keyword_en',
                'primary_keyword_ar',
                'secondary_keywords_en',
                'secondary_keywords_ar',
                'final_url_slug',
                'image_alt_en',
                'image_alt_ar',
                'og_title_ar',
                'og_description_en',
                'og_description_ar',
                'pdp_headline_en',
                'above_fold_hook_en',
                'keywords'
            ]);
        });
    }
};
