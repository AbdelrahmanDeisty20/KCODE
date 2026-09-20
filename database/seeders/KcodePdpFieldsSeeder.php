<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KcodePdpFieldsSeeder extends Seeder
{
    /**
     * Run the database seeds for KCODE PDP and QuickView fields using real JSON data when available.
     */
    public function run(): void
    {
        $jsonPaths = [
            base_path('exicel/kcode_products.json'),
            base_path('kcode_products.json'),
            'C:/Users/Dell/Downloads/KCODE_Developer_Pack_v16.5 (2)/kcode_modern_v16_5/kcode_products.json',
        ];

        $jsonProductsBySku = [];
        foreach ($jsonPaths as $p) {
            if (file_exists($p)) {
                $content = json_decode(file_get_contents($p), true);
                if (!empty($content['products'])) {
                    foreach ($content['products'] as $pData) {
                        if (!empty($pData['sku'])) {
                            $jsonProductsBySku[$pData['sku']] = $pData;
                        }
                    }
                }
                break;
            }
        }

        $products = Product::all();

        $textureMap = [
            'Lightweight' => 'خفيف وقابل للامتصاص السريع',
            'Medium'      => 'قوام متوسط التغذية',
            'Cream'       => 'كريمي غني ومغذي',
            'Gel'         => 'جل منعش وخفيف',
            'Oil'         => 'زيتي لطيف على البشرة',
        ];

        $frequencyMap = [
            'Daily'    => 'استخدام يومي (صباحاً ومساءً)',
            'Weekly'   => 'استخدام أسبوعي (1-2 مرة أسبوعياً)',
            'Spot'     => 'عند الحاجة على موضع المتاعب',
        ];

        foreach ($products as $product) {
            $jsonData = $jsonProductsBySku[$product->sku] ?? null;

            $textureVal = $jsonData['texture'] ?? 'Lightweight';
            $freqVal = $jsonData['frequency'] ?? 'Daily';
            $actives = $jsonData['customer_actives'] ?? ($jsonData['key_actives'] ?? null);
            $toleranceNum = (int) ($jsonData['tolerance'] ?? 75);

            $strengthLevel = 'Medium';
            if ($toleranceNum >= 70) {
                $strengthLevel = 'High';
            } elseif ($toleranceNum <= 40) {
                $strengthLevel = 'Low';
            }

            $product->update([
                'size'                  => ($jsonData['size'] ?? null) ?: ($product->size ?: '30ml'),
                'barcode'               => ($jsonData['barcode'] ?? null) ?: ($product->barcode ?: ('880967077' . str_pad($product->id, 4, '0', STR_PAD_LEFT))),
                'sensitive_eligible'    => true,
                'brief_insight_ar'      => $product->brief_insight_ar ?: 'سيروم مهدئ يعزز مرونة البشرة ويقلل الاحمرار والتهيج',
                'country_of_origin_ar'  => $product->country_of_origin_ar ?: 'كوريا الجنوبية',
                'role_ar'               => ($jsonData['routine_role'] ?? null) ?: ($product->role_ar ?: 'Daily Treatment'),
                'limitations_notes_ar'  => $product->limitations_notes_ar ?: 'يُحفظ في مكان بارد وجاف بعيداً عن أشعة الشمس المباشرة. يُنصح بإجراء اختبار حساسية على جزء صغير قبل الاستخدام الكامل.',
                
                // Detailed Product Fields
                'texture_ar'            => $textureMap[$textureVal] ?? $textureVal,
                'texture_en'            => $textureVal,
                'why_kcode_ar'          => $product->why_kcode_ar ?: 'تركيبة كورية أصلية معتمدة تم تقييم أمانها وملاءمتها للبشرة في مختبرات KCODE.',
                'why_kcode_en'          => $product->why_kcode_en ?: 'Authentic Korean formula safety-tested for skin compatibility by KCODE Labs.',
                'usage_frequency_ar'    => $frequencyMap[$freqVal] ?? $freqVal,
                'active_strength_level' => $strengthLevel,
                'safety_notes_ar'       => $product->safety_notes_ar ?: 'خالي من العطور الاصطناعية والمواد المهيجة مناسب للبشرة الحساسة.',
                'safety_notes_en'       => $product->safety_notes_en ?: 'Fragrance-free and low irritation risk for sensitive skin.',
                'ar_key_benefits'       => $actives ? "المكونات الفعالة الرئيسية: {$actives}" : 'تعزيز صحة البشرة والنضارة وتحسين مرونة الجلد',
                'en_key_benefits'       => $actives ? "Key Active Ingredients: {$actives}" : 'Enhance skin health, radiance and barrier strength',

                // SEO Fields
                'ar_product_title_seo'  => $product->ar_product_title_seo ?: $product->name_ar,
                'en_product_title_seo'  => $product->en_product_title_seo ?: $product->name_en,
                'en_short_hook'         => $product->en_short_hook ?: 'Authentic K-Beauty Skincare',
                'seo_meta_title_ar'     => $product->seo_meta_title_ar ?: ($product->name_ar . ' | KCODE'),
                'meta_description_ar'   => $product->meta_description_ar ?: ($product->description_ar ?: 'منتج كوري أصلي للعناية بالبشرة متوفر في متجر KCODE.'),
                'meta_description_en'   => $product->meta_description_en ?: ($product->description_en ?: 'Authentic Korean skincare product available on KCODE.'),
                'primary_keyword_ar'    => $product->primary_keyword_ar ?: 'عناية بالبشرة',
                'primary_keyword_en'    => $product->primary_keyword_en ?: 'K-Beauty',
                'final_url_slug'        => $product->final_url_slug ?: Str::slug($product->name_en),
            ]);
        }
    }
}
