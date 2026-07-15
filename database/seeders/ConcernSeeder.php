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
                'name_ar' => 'حبوب',
                'name_en' => 'Acne',
                'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'مسام واسعة',
                'name_en' => 'Enlarged Pores',
                'image' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'تصبغات',
                'name_en' => 'Pigmentation',
                'image' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'حساسية',
                'name_en' => 'Sensitivity',
                'image' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'إجهاد',
                'name_en' => 'Dullness',
                'image' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'خطوط دقيقة',
                'name_en' => 'Fine Lines',
                'image' => 'https://images.unsplash.com/photo-1616683693504-3ea7e9ad6fec?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'جفاف',
                'name_en' => 'Dryness',
                'image' => 'https://images.unsplash.com/photo-1601049676099-e7ed07d825b0?auto=format&fit=crop&q=80&w=400',
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
