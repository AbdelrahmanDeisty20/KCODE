<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class CategorySeeder extends Seeder
{
    /**
     * Category card descriptions keyed by name_en.
     *
     * AR: copied verbatim from KCODE_Homepage_Developer_V24_FINAL/homepage.config.json → types.cards[].desc
     *     (the pack rule: "انسخ النصوص من هذا الملف. لا تُعِد كتابتها في الكود.")
     * EN: no official English copy exists in the pack — literal translation of the AR text, pending approval.
     */
    public static function descriptions(): array
    {
        return [
            'Cleanser'    => ['ar' => 'ينظّف الوجه ويزيل الزيوت والأوساخ', 'en' => 'Cleanses the face and removes oil and dirt'],
            'Toner'       => ['ar' => 'يوازن البشرة بعد التنظيف ويهيّئها لما بعده', 'en' => 'Balances skin after cleansing and preps it for what comes next'],
            'Essence'     => ['ar' => 'طبقة ترطيب خفيفة قبل السيروم', 'en' => 'A light hydrating layer before serum'],
            'Serum'       => ['ar' => 'تركيز عالٍ لمعالجة مشكلة محدّدة', 'en' => 'High concentration to target a specific concern'],
            'Moisturizer' => ['ar' => 'يحبس الترطيب ويحمي حاجز البشرة', 'en' => 'Locks in hydration and protects the skin barrier'],
            'Sunscreen'   => ['ar' => 'الحماية اليومية من أشعة الشمس', 'en' => 'Daily protection from the sun'],
            'Eye Care'    => ['ar' => 'تركيبات ألطف لمحيط العين الرقيق', 'en' => 'Gentler formulas for the delicate eye area'],
            'Exfoliator'  => ['ar' => 'يزيل الخلايا الميتة ويحسّن الملمس', 'en' => 'Removes dead skin cells and improves texture'],
            'Mask'        => ['ar' => 'عناية مركّزة أسبوعية', 'en' => 'Weekly intensive care'],
            'Body Care'   => ['ar' => 'تنظيف الجسم والعناية بالحبوب والملمس', 'en' => 'Body cleansing and care for breakouts and texture'],
        ];
    }

    /**
     * Seed the official 10 KCODE Product Types from V24 Developer Pack.
     */
    public function run(): void
    {
        // Truncate existing categories to guarantee strictly 10 categories
        Schema::disableForeignKeyConstraints();
        SubCategory::truncate();
        Category::truncate();
        Schema::enableForeignKeyConstraints();

        $categories = [
            ['name_en' => 'Cleanser', 'name_ar' => 'غسول', 'image' => 'cleanser.webp'],
            ['name_en' => 'Toner', 'name_ar' => 'تونر', 'image' => 'toner.webp'],
            ['name_en' => 'Essence', 'name_ar' => 'إسنس', 'image' => 'essence.webp'],
            ['name_en' => 'Serum', 'name_ar' => 'سيروم وأمبول', 'image' => 'serum-ampoule.webp'],
            ['name_en' => 'Moisturizer', 'name_ar' => 'مرطب', 'image' => 'moisturizer.webp'],
            ['name_en' => 'Sunscreen', 'name_ar' => 'واقي شمس', 'image' => 'sunscreen.webp'],
            ['name_en' => 'Eye Care', 'name_ar' => 'العناية بالعين', 'image' => 'eye-care.webp'],
            ['name_en' => 'Exfoliator', 'name_ar' => 'مقشر', 'image' => 'exfoliator.webp'],
            ['name_en' => 'Mask', 'name_ar' => 'ماسكات', 'image' => 'mask.webp'],
            ['name_en' => 'Body Care', 'name_ar' => 'العناية بالجسم', 'image' => 'body-care.webp'],
        ];

        $sourceDir = 'c:/Users/Dell/Downloads/KCODE_Homepage_Developer_V24_FINAL/KCODE_Homepage_Developer_V24_FINAL/assets/product-types';
        $targetDir = storage_path('app/public/categories');

        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true, true);
        }

        foreach ($categories as $cat) {
            // Copy image if available in assets package
            $srcFile = $sourceDir . '/' . $cat['image'];
            $destFile = $targetDir . '/' . $cat['image'];

            if (File::exists($srcFile)) {
                File::copy($srcFile, $destFile);
            }

            Category::create([
                'name_en' => $cat['name_en'],
                'name_ar' => $cat['name_ar'],
                'description_ar' => self::descriptions()[$cat['name_en']]['ar'] ?? null,
                'description_en' => self::descriptions()[$cat['name_en']]['en'] ?? null,
                'image'   => $cat['image'],
            ]);
        }
    }
}
