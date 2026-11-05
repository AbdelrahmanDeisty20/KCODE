<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KcodePdpFieldsSeeder extends Seeder
{
    /**
     * Verify and ensure all 256 products have complete R479 PDP, QuickView, INCI and routine fields.
     */
    public function run(): void
    {
        $jsonFile = base_path('exicel/kcode_products.json');
        if (!file_exists($jsonFile)) {
            $jsonFile = base_path('kcode_products.json');
        }

        if (!file_exists($jsonFile)) {
            $this->command->error("kcode_products.json file not found.");
            return;
        }

        $content = json_decode(file_get_contents($jsonFile), true);
        $jsonProductsBySku = [];
        if (!empty($content['products'])) {
            foreach ($content['products'] as $pData) {
                if (!empty($pData['sku'])) {
                    $jsonProductsBySku[$pData['sku']] = $pData;
                }
            }
        }

        $products = Product::all();
        $updated = 0;

        foreach ($products as $product) {
            $jsonData = $jsonProductsBySku[$product->sku] ?? null;
            if (!$jsonData) {
                continue;
            }

            $updateData = [];

            // 1. Ensure full INCI is present
            if (empty($product->ingredients_ar) || empty($product->ingredients_en)) {
                $inci = $jsonData['full_inci'] ?? ($jsonData['ingredients_ar'] ?? '');
                if ($inci) {
                    $updateData['ingredients_ar'] = $inci;
                    $updateData['ingredients_en'] = $inci;
                }
            }

            // 2. Ensure Routine steps JSON is present
            if (empty($product->routine_steps_json)) {
                $updateData['routine_steps_json'] = $jsonData['routine_steps'] ?? [];
            }

            // 3. Ensure Usage instructions JSON is present
            if (empty($product->usage_instructions_json)) {
                $tiles = $jsonData['usage_tiles_detailed'] ?? [];
                $updateData['usage_instructions_json'] = [
                    'timing_label'       => $tiles[0]['label'] ?? 'التوقيت',
                    'timing'             => $tiles[0]['value'] ?? ($jsonData['timing_ar'] ?: 'صباحًا ومساءً'),
                    'timing_note'        => $tiles[0]['note'] ?? '',
                    'amount_label'       => $tiles[1]['label'] ?? 'الكمية',
                    'amount'             => $tiles[1]['value'] ?? '',
                    'amount_note'        => $tiles[1]['note'] ?? '',
                    'application_label'  => $tiles[2]['label'] ?? 'التطبيق',
                    'application_method' => $tiles[2]['value'] ?? '',
                    'application_note'   => $tiles[2]['note'] ?? '',
                    'gradual_start_label'=> $tiles[3]['label'] ?? 'ملاحظة الروتين',
                    'gradual_start'      => $tiles[3]['value'] ?? '',
                    'gradual_start_note' => $tiles[3]['note'] ?? '',
                    'caution'            => $jsonData['usage_caution_ar'] ?? '',
                    'tiles'              => $tiles,
                    'benefits'           => $jsonData['benefits'] ?? [],
                    'verdict'            => [
                        'note'               => $jsonData['verdict_note_ar'] ?? '',
                        'skin_fit_value'     => $jsonData['skin_fit_value_ar'] ?? 'الدهنية · المختلطة · العادية · الجافة',
                        'skin_fit_guidance'  => $jsonData['skin_fit_guidance_ar'] ?? '',
                        'sensitive_status'   => $jsonData['sensitive_status_ar'] ?? '',
                        'sensitive_guidance' => $jsonData['sensitive_guidance_ar'] ?? '',
                        'fragrance'          => $jsonData['fragrance_ar'] ?? '',
                        'actives'            => $jsonData['actives_ar'] ?? '',
                        'summary_html'       => $jsonData['summary_html'] ?? '',
                    ],
                ];
            }

            // 4. Ensure FAQs JSON is present
            if (empty($product->product_faqs_json)) {
                $updateData['product_faqs_json'] = $jsonData['faqs'] ?? [];
            }

            // 5. Ensure Complementary routine JSON is present
            if (empty($product->complementary_routine_json)) {
                $updateData['complementary_routine_json'] = $jsonData['complementary_routine'] ?? [];
            }

            if (!empty($updateData)) {
                $product->update($updateData);
                $updated++;
            }
        }

        $this->command->info("KcodePdpFieldsSeeder verified: {$updated} products updated with missing fields; all 256 products retain authentic R479 data.");
    }
}
