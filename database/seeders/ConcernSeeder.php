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
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Concern::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $concerns = [
            [
                'name_ar' => 'الحبوب والشوائب',
                'name_en' => 'Acne & Blemishes',
                'description_ar' => 'تنقية البشرة ومكافحة البثور وتنظيم الإفرازات الدهنية.',
                'description_en' => 'Purifying the skin, fighting blemishes, and regulating excess sebum production.',
                'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'المسام الرؤوس السوداء',
                'name_en' => 'Pores & Blackheads',
                'description_ar' => 'تنظيف عميق للمسام وتقليل مظهرها المزعج وتحسين الملمس.',
                'description_en' => 'Deep pore cleansing, minimizing their appearance, and refining skin texture.',
                'image' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'التصبغات والبقع الداكنة',
                'name_en' => 'Pigmentation & Dark Spots',
                'description_ar' => 'توحيد لون البشرة وتخفيف البقع الناتجة عن الشمس وآثار الحبوب.',
                'description_en' => 'Evening out skin tone and fading spots caused by sun exposure and post-acne marks.',
                'image' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'الاحمرار والتهيج',
                'name_en' => 'Redness & Irritation',
                'description_ar' => 'تهدئة البشرة الحساسة وتخفيف الاحمرار الناتج عن العوامل البيئية.',
                'description_en' => 'Soothing sensitive skin and relieving redness triggered by environmental stressors.',
                'image' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'حاجز البشرة',
                'name_en' => 'Skin Barrier',
                'description_ar' => 'تقوية وتدعيم حاجز الحماية الطبيعي لحماية البشرة من الجفاف والتلف.',
                'description_en' => 'Strengthening and reinforcing the natural protective barrier to shield skin from dryness and damage.',
                'image' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'التجاعيد والخطوط التعبيرية',
                'name_en' => 'Wrinkles & Fine Lines',
                'description_ar' => 'شد البشرة وتحفيز الكولاجين لمقاومة علامات التقدم في السن.',
                'description_en' => 'Firming the skin and stimulating collagen production to combat signs of aging.',
                'image' => 'https://images.unsplash.com/photo-1616683693504-3ea7e9ad6fec?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'الجفاف والترطيب',
                'name_en' => 'Dryness & Hydration',
                'description_ar' => 'ترطيب عميق طويل الأمد لملء البشرة واستعادة مرونتها.',
                'description_en' => 'Deep, long-lasting hydration to plump the skin and restore its natural elasticity.',
                'image' => 'https://images.unsplash.com/photo-1601049676099-e7ed07d825b0?auto=format&fit=crop&q=80&w=400',
            ],
        ];

        foreach ($concerns as $concern) {
            $filename = \Illuminate\Support\Str::slug($concern['name_en']) . '.jpg';
            ImageDownloader::downloadAndSave($concern['image'], 'concerns', $filename);

            Concern::create([
                'name_en' => $concern['name_en'],
                'name_ar' => $concern['name_ar'],
                'description_en' => $concern['description_en'],
                'description_ar' => $concern['description_ar'],
                'image' => $filename,
                'status' => 'active',
            ]);
        }
    }
}
