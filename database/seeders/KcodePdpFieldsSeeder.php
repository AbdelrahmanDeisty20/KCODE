<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

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

        foreach ($products as $product) {
            $jsonData = $jsonProductsBySku[$product->sku] ?? null;

            $product->update([
                'size'                 => ($jsonData['size'] ?? null) ?: ($product->size ?: '30ml'),
                'barcode'              => ($jsonData['barcode'] ?? null) ?: ($product->barcode ?: ('880967077' . str_pad($product->id, 4, '0', STR_PAD_LEFT))),
                'sensitive_eligible'   => true,
                'brief_insight_ar'     => $product->brief_insight_ar ?: 'سيروم مهدئ يعزز مرونة البشرة ويقلل الاحمرار والتهيج',
                'country_of_origin_ar' => $product->country_of_origin_ar ?: 'كوريا الجنوبية',
                'role_ar'              => ($jsonData['routine_role'] ?? null) ?: ($product->role_ar ?: 'تهدئة التهيج وتخفيف الاحمرار وتعزيز الحاجز البشري'),
                'limitations_notes_ar' => $product->limitations_notes_ar ?: 'يُحفظ في مكان بارد وجاف بعيداً عن أشعة الشمس المباشرة. يُنصح بإجراء اختبار حساسية على جزء صغير قبل الاستخدام الكامل.',
            ]);
        }
    }
}
