<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name_en' => 'Cleanser', 'name_ar' => 'غسول'],
            ['name_en' => 'Toner', 'name_ar' => 'تونر'],
            ['name_en' => 'Serum', 'name_ar' => 'سيروم'],
            ['name_en' => 'Moisturizer', 'name_ar' => 'مرطب'],
            ['name_en' => 'Sunscreen', 'name_ar' => 'واقي شمس'],
            ['name_en' => 'Face Mask', 'name_ar' => 'ماسك للوجه'],
            ['name_en' => 'Eye Cream', 'name_ar' => 'كريم للعين'],
            ['name_en' => 'Exfoliator', 'name_ar' => 'مقشر للبشرة'],
            ['name_en' => 'Lip Balm', 'name_ar' => 'مرطب شفاه'],
            ['name_en' => 'Face Oil', 'name_ar' => 'زيت للوجه'],
            ['name_en' => 'Makeup Remover', 'name_ar' => 'مزيل مكياج'],
            ['name_en' => 'Essence', 'name_ar' => 'إيسنس للبشرة'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name_en' => $category['name_en']],
                ['name_ar' => $category['name_ar']]
            );
        }
    }
}
