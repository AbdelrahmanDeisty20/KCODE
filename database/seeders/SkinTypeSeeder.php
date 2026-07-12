<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\SkinType;

class SkinTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skinTypes = [
            [
                'name_ar' => 'دهنية',
                'name_en' => 'Oily',
                'description_ar' => 'إفراز مفرط للدهون ولمعان مع مسام واضحة ولزجة',
                'description_en' => 'Excessive sebum production and shine with visible, sticky pores.',
                'image' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'جافة',
                'name_en' => 'Dry',
                'description_ar' => 'بشرة مشدودة، خشنة الملمس وتحتاج لترطيب كريمي غني',
                'description_en' => 'Tight skin, rough texture, and requires rich creamy hydration.',
                'image' => 'https://images.unsplash.com/photo-1616683693504-3ea7e9ad6fec?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'مختلطة',
                'name_en' => 'Combination',
                'description_ar' => 'منطقة T (الجبهة والأنف) دهنية والوجنتين جافتين أو عاديتين',
                'description_en' => 'Oily T-zone (forehead and nose) and dry or normal cheeks.',
                'image' => 'https://images.unsplash.com/photo-1512290923902-8a9f81dc236c?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'حساسة',
                'name_en' => 'Sensitive',
                'description_ar' => 'تتأثر سريعاً بالعوامل الخارجية ومعرضة للاحمرار والوخز',
                'description_en' => 'Reacts quickly to external factors and is prone to redness and stinging.',
                'image' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&q=80&w=400',
            ],
        ];

        foreach ($skinTypes as $type) {
            SkinType::updateOrCreate(['name_en' => $type['name_en']], $type);
        }
    }
}
