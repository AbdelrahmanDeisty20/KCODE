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
                'image'   => $cat['image'],
            ]);
        }
    }
}
