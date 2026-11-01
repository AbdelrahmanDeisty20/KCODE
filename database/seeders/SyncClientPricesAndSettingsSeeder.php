<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class SyncClientPricesAndSettingsSeeder extends Seeder
{
    /**
     * Synchronize product prices and key store settings from client official prototype.
     */
    public function run(): void
    {
        $this->command->info("Starting synchronization with client official prototype...");

        // 1. Locate client data (from decompressed scratch or original HTML file)
        $decompressedPath = 'C:/Users/Dell/.gemini/antigravity-ide/brain/4e67432b-ac09-4604-8b7e-2b8df6d1dafd/scratch/decompressed_client.html';
        $originalHtmlPath = 'C:/Users/Dell/Downloads/KCODE_R422_I_DESKTOP (2).html';

        $clientProducts = [];

        if (file_exists($decompressedPath)) {
            $json = json_decode(file_get_contents($decompressedPath), true);
            $clientProducts = $json['products'] ?? [];
        }

        if (empty($clientProducts) && file_exists($originalHtmlPath)) {
            $raw = file_get_contents($originalHtmlPath);
            if (preg_match('/<script id="kcode-payload" type="application\/json">(.*?)<\/script>/s', $raw, $matches)) {
                $payload = json_decode($matches[1], true);
                if (!empty($payload['boot']['inline'])) {
                    $decompressed = gzdecode(base64_decode($payload['boot']['inline']));
                    $bootJson = json_decode($decompressed, true);
                    $clientProducts = $bootJson['products'] ?? [];
                }
            }
        }

        if (empty($clientProducts)) {
            $this->command->error("Could not load client products from prototype file.");
            return;
        }

        $this->command->info("Found " . count($clientProducts) . " products in client prototype data.");

        // 2. Synchronize Prices
        $updatedCount = 0;
        $skippedCount = 0;

        foreach ($clientProducts as $sku => $data) {
            $product = Product::where('sku', $sku)->first();
            if (!$product) {
                $skippedCount++;
                continue;
            }

            $clientPrice = isset($data['price']) ? (float) $data['price'] : null;
            if ($clientPrice !== null && $clientPrice > 0) {
                $product->price = $clientPrice;

                // Sync stock if present
                if (isset($data['inventory']) && is_numeric($data['inventory'])) {
                    $product->stock = (int) $data['inventory'];
                }

                $product->save();
                $updatedCount++;
            }
        }

        $this->command->info("Successfully updated prices for {$updatedCount} products. Skipped {$skippedCount} unmapped products.");

        // 3. Synchronize Official Store Settings
        $settingsToUpdate = [
            'announcement_text' => [
                'key_ar'   => 'نص الإعلان العلوي',
                'value_ar' => 'شحن مجاني للطلبات بقيمة 25 ر.ع. فأكثر',
                'value_en' => 'Free shipping on orders of 25 OMR or more',
            ],
            'currency_symbol' => [
                'key_ar'   => 'رمز العملة',
                'value_ar' => 'ر.ع.',
                'value_en' => 'OMR',
            ],
            'free_shipping_min_amount' => [
                'key_ar'   => 'الحد الأدنى للشحن المجاني',
                'value_ar' => '25',
                'value_en' => '25',
            ],
            'authenticity_info_ar' => [
                'key_ar'   => 'نص ضمان الأصالة',
                'value_ar' => 'من العلامات التجارية مباشرةً أو مورّديها الرسميين في كوريا.',
                'value_en' => 'Directly from brands or official suppliers in Korea.',
            ],
            'delivery_info_ar' => [
                'key_ar'   => 'نص معلومات الشحن والتوصيل',
                'value_ar' => 'إلى جميع محافظات سلطنة عُمان خلال 24 إلى 48 ساعة من تأكيد الطلب.',
                'value_en' => 'To all governorates of Oman within 24 to 48 hours of order confirmation.',
            ],
            'return_policy_info_ar' => [
                'key_ar'   => 'نص سياسة الإرجاع والمرونة',
                'value_ar' => 'ادفع بالبطاقة أو عند الاستلام.',
                'value_en' => 'Pay by card or cash on delivery.',
            ],
        ];

        foreach ($settingsToUpdate as $keyEn => $data) {
            Setting::updateOrCreate(
                ['key_en' => $keyEn],
                [
                    'key_ar'   => $data['key_ar'],
                    'value_ar' => $data['value_ar'],
                    'value_en' => $data['value_en'],
                ]
            );
        }

        $this->command->info("Store settings successfully synchronized with client official copy.");
    }
}
