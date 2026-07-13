<?php

namespace Database\Seeders;

use App\Models\Concern;
use Illuminate\Database\Seeder;

class ConcernSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $concerns = [
            [
                'name_ar' => 'حب الشباب والبثور',
                'name_en' => 'Acne & Blemishes',
                'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'التجاعيد وعلامات التقدم في السن',
                'name_en' => 'Aging & Wrinkles',
                'image' => 'https://images.unsplash.com/photo-1616683693504-3ea7e9ad6fec?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'الجفاف والخشونة',
                'name_en' => 'Dryness & Dehydration',
                'image' => 'https://images.unsplash.com/photo-1601049676099-e7ed07d825b0?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'البقع الداكنة والتصبغات',
                'name_en' => 'Dark Spots & Hyperpigmentation',
                'image' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'المسام الواسعة',
                'name_en' => 'Large Pores',
                'image' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'الاحمرار وحساسية البشرة',
                'name_en' => 'Redness & Sensitivity',
                'image' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'البهتان وعدم توحد الملمس',
                'name_en' => 'Dullness & Uneven Texture',
                'image' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'إفراز الدهون الزائدة واللمعان',
                'name_en' => 'Excess Oil & Shine',
                'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'الرؤوس السوداء والبيضاء',
                'name_en' => 'Blackheads & Whiteheads',
                'image' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'أضرار أشعة الشمس والتلف',
                'name_en' => 'Sun Damage',
                'image' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'الهالات السوداء وانتفاخ العينين',
                'name_en' => 'Dark Circles & Puffiness',
                'image' => 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'فقدان مرونة وترهل البشرة',
                'name_en' => 'Loss of Elasticity & Firmness',
                'image' => 'https://images.unsplash.com/photo-1616683693504-3ea7e9ad6fec?auto=format&fit=crop&q=80&w=400',
            ],
        ];

        foreach ($concerns as $concern) {
            Concern::updateOrCreate(
                ['name_en' => $concern['name_en']],
                [
                    'name_ar' => $concern['name_ar'],
                    'image' => $concern['image'],
                ]
            );
        }
    }
}
