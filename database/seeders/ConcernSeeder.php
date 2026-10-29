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
                'id'             => 1,
                'name_ar'        => 'رؤوس سوداء ومسام واضحة',
                'name_en'        => 'Blackheads & Visible Pores',
                'description_ar' => 'تنقية المسام المسدودة وتقليل الرؤوس السوداء وتحسين ملمس البشرة.',
                'description_en' => 'Purifying clogged pores, minimizing blackheads, and refining skin texture.',
                'image'          => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'id'             => 2,
                'name_ar'        => 'دهون زائدة ولمعان',
                'name_en'        => 'Excess Oil & Shine',
                'description_ar' => 'السيطرة على الإفراز الدهني وضبط اللمعان وموازنة الزيوت في الوجه.',
                'description_en' => 'Controlling excess sebum production, regulating shine, and balancing facial oils.',
                'image'          => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'id'             => 3,
                'name_ar'        => 'آثار حبوب حمراء',
                'name_en'        => 'Red Acne Marks (PIE)',
                'description_ar' => 'تخفيف البقع الوردية والحمراء المتبقية بعد شفاء الحبوب وتهدئتها.',
                'description_en' => 'Fading post-inflammatory erythema (pink and red marks) left after breakouts heal.',
                'image'          => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'id'             => 4,
                'name_ar'        => 'بقع داكنة وتصبغات',
                'name_en'        => 'Dark Spots & Pigmentation (PIH)',
                'description_ar' => 'تفتيح التصبغات والبقع البنية الناتجة عن الشمس وآثار الحبوب وتوحيد اللون.',
                'description_en' => 'Evening out skin tone and fading dark spots caused by sun exposure and post-acne marks.',
                'image'          => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'id'             => 5,
                'name_ar'        => 'جفاف وشدّ',
                'name_en'        => 'Dryness & Tightness',
                'description_ar' => 'تعويض نقص الترطيب العميق وتقوية حاجز البشرة وإلغاء إحساس الانكماش.',
                'description_en' => 'Deep hydration, restoring natural skin barrier, and relieving skin tightness.',
                'image'          => 'https://images.unsplash.com/photo-1601049676099-e7ed07d825b0?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'id'             => 6,
                'name_ar'        => 'احمرار مستمر',
                'name_en'        => 'Persistent Redness',
                'description_ar' => 'تهدئة الاحمرار والالتهاب وتسكين البشرة سريعة الانفعال ودعم الشعيرات السطحية.',
                'description_en' => 'Calming continuous facial redness, soothing irritation, and comforting reactive skin.',
                'image'          => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'id'             => 7,
                'name_ar'        => 'بهتان وشحوب',
                'name_en'        => 'Dullness & Lack of Glow',
                'description_ar' => 'تنشيط حيوية وخلايا البشرة واستعادة الإشراقة الطبيعية والنضارة الصحية.',
                'description_en' => 'Revitalizing skin cell turnover and restoring a radiant, healthy natural glow.',
                'image'          => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'id'             => 8,
                'name_ar'        => 'خطوط دقيقة',
                'name_en'        => 'Fine Lines',
                'description_ar' => 'تنعيم مظهر الخطوط التعبيرية المبكرة وتحفيز الكولاجين لمقاومة علامات التقدم في السن.',
                'description_en' => 'Smoothing early expression fine lines and stimulating collagen to combat aging.',
                'image'          => 'https://images.unsplash.com/photo-1616683693504-3ea7e9ad6fec?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'id'             => 9,
                'name_ar'        => 'مشاكل حول العين',
                'name_en'        => 'Eye Contour Concerns',
                'description_ar' => 'العناية بالمنطقة الحساسة حول العين وتخفيف الهالات السوداء والانتفاخ والخطوط.',
                'description_en' => 'Caring for delicate eye area, targeting dark circles, puffiness, and fine lines.',
                'image'          => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&q=80&w=400',
            ],
        ];

        foreach ($concerns as $concern) {
            $filename = \Illuminate\Support\Str::slug($concern['name_en']) . '.jpg';
            ImageDownloader::downloadAndSave($concern['image'], 'concerns', $filename, false);

            Concern::create([
                'id'             => $concern['id'],
                'name_en'        => $concern['name_en'],
                'name_ar'        => $concern['name_ar'],
                'description_en' => $concern['description_en'],
                'description_ar' => $concern['description_ar'],
                'image'          => $filename,
                'status'         => 'active',
            ]);
        }

        // Auto-link matching products to Concern 8 (Fine Lines) and 9 (Eye Contour)
        $fineLineProducts = \App\Models\Product::where('name_ar', 'LIKE', '%تجاعيد%')
            ->orWhere('name_ar', 'LIKE', '%خطوط%')
            ->orWhere('name_ar', 'LIKE', '%شد%')
            ->orWhere('name_en', 'LIKE', '%wrinkle%')
            ->orWhere('name_en', 'LIKE', '%aging%')
            ->orWhere('name_en', 'LIKE', '%retinol%')
            ->orWhere('name_en', 'LIKE', '%peptide%')
            ->pluck('id');

        foreach ($fineLineProducts as $pId) {
            \Illuminate\Support\Facades\DB::table('product_concerns')->insertOrIgnore([
                'product_id' => $pId,
                'concern_id' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $eyeProducts = \App\Models\Product::where('name_ar', 'LIKE', '%عين%')
            ->orWhere('name_ar', 'LIKE', '%هالات%')
            ->orWhere('name_en', 'LIKE', '%eye%')
            ->pluck('id');

        foreach ($eyeProducts as $pId) {
            \Illuminate\Support\Facades\DB::table('product_concerns')->insertOrIgnore([
                'product_id' => $pId,
                'concern_id' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
