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
                'name_ar'        => 'الحبوب والبثور',
                'name_en'        => 'Acne & Blemishes',
                'description_ar' => 'علاج الحبوب النشطة والبثور وتقليل الالتهاب وتنقية البشرة.',
                'description_en' => 'Treating active breakouts and blemishes, reducing inflammation, and purifying the skin.',
                'image'          => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'id'             => 2,
                'name_ar'        => 'المسام والرؤوس السوداء',
                'name_en'        => 'Pores & Blackheads',
                'description_ar' => 'تنقية المسام المسدودة وتقليل الرؤوس السوداء وتحسين ملمس البشرة.',
                'description_en' => 'Purifying clogged pores, minimizing blackheads, and refining skin texture.',
                'image'          => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'id'             => 3,
                'name_ar'        => 'التصبغات وآثار الحبوب',
                'name_en'        => 'Pigmentation & Dark Spots',
                'description_ar' => 'تفتيح التصبغات والبقع الداكنة وآثار الحبوب وتوحيد لون البشرة.',
                'description_en' => 'Evening out skin tone and fading dark spots and post-acne marks.',
                'image'          => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'id'             => 4,
                'name_ar'        => 'الحساسية والاحمرار',
                'name_en'        => 'Redness & Irritation',
                'description_ar' => 'تهدئة البشرة الحساسة وتخفيف الاحمرار والتهيج ودعم راحة البشرة.',
                'description_en' => 'Calming sensitive skin, soothing redness and irritation, and comforting reactive skin.',
                'image'          => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'id'             => 5,
                'name_ar'        => 'ضعف حاجز البشرة',
                'name_en'        => 'Skin Barrier',
                'description_ar' => 'تقوية وتدعيم حاجز البشرة الطبيعي لحمايتها من الجفاف والتلف والعوامل الخارجية.',
                'description_en' => 'Strengthening and reinforcing the natural skin barrier to protect against dryness and damage.',
                'image'          => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'id'             => 6,
                'name_ar'        => 'الجفاف والترطيب',
                'name_en'        => 'Dryness & Hydration',
                'description_ar' => 'تعويض نقص الترطيب العميق ومنح البشرة مرونة ونعومة تدوم طويلاً.',
                'description_en' => 'Deep, long-lasting hydration to replenish moisture and restore skin softness and elasticity.',
                'image'          => 'https://images.unsplash.com/photo-1601049676099-e7ed07d825b0?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'id'             => 7,
                'name_ar'        => 'البهتان والإشراقة',
                'name_en'        => 'Dullness & Lack of Glow',
                'description_ar' => 'تنشيط حيوية البشرة واستعادة الإشراقة الطبيعية والنضارة الصحية.',
                'description_en' => 'Revitalizing skin vitality and restoring a radiant, healthy natural glow.',
                'image'          => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'id'             => 8,
                'name_ar'        => 'الخطوط الدقيقة والتجاعيد',
                'name_en'        => 'Wrinkles & Fine Lines',
                'description_ar' => 'تنعيم مظهر الخطوط الدقيقة والتجاعيد وتحسين مرونة البشرة.',
                'description_en' => 'Smoothing fine lines and wrinkles and improving skin elasticity to combat signs of aging.',
                'image'          => 'https://images.unsplash.com/photo-1616683693504-3ea7e9ad6fec?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'id'             => 9,
                'name_ar'        => 'العناية بمحيط العين',
                'name_en'        => 'Eye Contour Concerns',
                'description_ar' => 'العناية بالمنطقة الحساسة حول العين وتخفيف الهالات السوداء والانتفاخ والخطوط.',
                'description_en' => 'Caring for the delicate eye area, targeting dark circles, puffiness, and fine lines.',
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

        // Rebuild product_concerns links if products exist in DB
        if (\App\Models\Product::count() > 0) {
            \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
            \App\Models\ProductConcern::truncate();
            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

            $jsonPaths = [
                base_path('exicel/kcode_products.json'),
                base_path('kcode_products.json'),
            ];
            $jsonFile = null;
            foreach ($jsonPaths as $p) {
                if (file_exists($p)) {
                    $jsonFile = $p;
                    break;
                }
            }

            if ($jsonFile) {
                $jsonContent = json_decode(file_get_contents($jsonFile), true);
                $jsonProducts = $jsonContent['products'] ?? [];

                $concernKeyMap = [
                    'acne'             => 1, // الحبوب والبثور
                    'oil_control'      => 2, // المسام والرؤوس السوداء
                    'pores'            => 2, // المسام والرؤوس السوداء
                    'pigmentation_pih' => 3, // التصبغات وآثار الحبوب
                    'redness_pie'      => 4, // الحساسية والاحمرار
                    'barrier'          => 5, // ضعف حاجز البشرة
                    'hydration'        => 6, // الجفاف والترطيب
                    'radiance'         => 7, // البهتان والإشراقة
                    'lines'            => 8, // الخطوط الدقيقة والتجاعيد
                    'eye_dark_circles' => 9, // العناية بمحيط العين
                    'eye_puffiness'    => 9, // العناية بمحيط العين
                ];

                foreach ($jsonProducts as $pData) {
                    $sku = trim($pData['sku'] ?? '');
                    if (empty($sku)) continue;

                    $product = \App\Models\Product::where('sku', $sku)->first();
                    if (!$product) continue;

                    $goals = $pData['goals'] ?? [];
                    $matchedIds = [];

                    foreach ($concernKeyMap as $jsonKey => $concernId) {
                        if (($goals[$jsonKey] ?? 0) >= 50 && !in_array($concernId, $matchedIds)) {
                            $matchedIds[] = $concernId;
                            \Illuminate\Support\Facades\DB::table('product_concerns')->insertOrIgnore([
                                'product_id' => $product->id,
                                'concern_id' => $concernId,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }

            // Also ensure Fine Lines (8) and Eye Contour (9) products are linked by keywords
            $fineLineProducts = \App\Models\Product::where('name_ar', 'LIKE', '%تجاعيد%')
                ->orWhere('name_ar', 'LIKE', '%خطوط%')
                ->orWhere('name_ar', 'LIKE', '%شد%')
                ->orWhere('description_ar', 'LIKE', '%الخطوط الدقيقة%')
                ->orWhere('name_en', 'LIKE', '%wrinkle%')
                ->orWhere('name_en', 'LIKE', '%aging%')
                ->orWhere('name_en', 'LIKE', '%retinol%')
                ->orWhere('name_en', 'LIKE', '%retinal%')
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
                ->orWhere('description_ar', 'LIKE', '%محيط العين%')
                ->orWhere('description_ar', 'LIKE', '%حول العين%')
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

        // Sync Quiz Question 4 options mapped_id if already seeded
        $q4Mapping = [
            'Blackheads & Visible Pores'      => 2,
            'Excess Oil & Shine'              => 2,
            'Red Acne Marks (PIE)'            => 1,
            'Dark Spots & Pigmentation (PIH)' => 3,
            'Dryness & Tightness'             => 6,
            'Persistent Redness'              => 4,
            'Dullness & Lack of Glow'         => 7,
            'Fine Lines'                      => 8,
            'Eye Contour Concerns'            => 9,
        ];
        foreach ($q4Mapping as $titleEn => $concernId) {
            \App\Models\QuizOption::where('option_type', 'concern')
                ->where('title_en', $titleEn)
                ->update(['mapped_id' => $concernId]);
        }
    }
}
