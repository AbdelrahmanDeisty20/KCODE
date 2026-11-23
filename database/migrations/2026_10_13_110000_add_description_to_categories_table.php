<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'description_ar')) {
                $table->text('description_ar')->nullable()->after('name_en');
            }
            if (!Schema::hasColumn('categories', 'description_en')) {
                $table->text('description_en')->nullable()->after('description_ar');
            }
        });

        // Backfill the 10 official KCODE categories (only where empty)
        $descriptions = [
            'Cleanser'    => ['ar' => 'ينظّف الوجه ويزيل الزيوت والأوساخ',                   'en' => 'Cleanses the face and removes oil and dirt'],
            'Toner'       => ['ar' => 'يوازن البشرة بعد التنظيف ويهيّئها لما بعده',           'en' => 'Rebalances skin after cleansing and preps it for the next step'],
            'Essence'     => ['ar' => 'طبقة ترطيب خفيفة قبل السيروم',                        'en' => 'A lightweight hydration layer before serum'],
            'Serum'       => ['ar' => 'تركيز عالٍ لمعالجة مشكلة محدّدة',                     'en' => 'High concentration to target a specific concern'],
            'Moisturizer' => ['ar' => 'يحبس الترطيب ويحمي حاجز البشرة',                     'en' => 'Locks in hydration and protects the skin barrier'],
            'Sunscreen'   => ['ar' => 'الحماية اليومية من أشعة الشمس',                       'en' => 'Daily protection against the sun'],
            'Eye Care'    => ['ar' => 'تركيبات ألطف لمحيط العين الرقيق',                     'en' => 'Gentler formulas for the delicate eye area'],
            'Exfoliator'  => ['ar' => 'يزيل الخلايا الميتة ويحسّن الملمس',                  'en' => 'Removes dead skin cells and refines texture'],
            'Mask'        => ['ar' => 'عناية مركّزة أسبوعية',                                'en' => 'Concentrated weekly treatment'],
            'Body Care'   => ['ar' => 'تنظيف الجسم والعناية بالحبوب والملمس',               'en' => 'Body cleansing and care for texture and blemishes'],
        ];

        foreach ($descriptions as $nameEn => $desc) {
            DB::table('categories')
                ->where('name_en', $nameEn)
                ->whereNull('description_en')
                ->update([
                    'description_ar' => $desc['ar'],
                    'description_en' => $desc['en'],
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['description_ar', 'description_en']);
        });
    }
};
