<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SyncClientPricesAndSettingsSeeder extends Seeder
{
    /**
     * Self-contained Seeder: Product prices and store settings embedded directly.
     * No external JSON or files needed - runs seamlessly on any server/host.
     */
    public function run(): void
    {
        $this->command->info("Starting synchronization with client official prototype...");

        // Embedded official prices & inventory dataset from client prototype (224 products)
        $clientProducts = [
            'KC0001' => ['price' => 9.9, 'inventory' => 8],
            'KC0002' => ['price' => 9.9, 'inventory' => 99],
            'KC0003' => ['price' => 9.9, 'inventory' => 8],
            'KC0004' => ['price' => 12.0, 'inventory' => null],
            'KC0005' => ['price' => 13.0, 'inventory' => null],
            'KC0006' => ['price' => 9.0, 'inventory' => null],
            'KC0007' => ['price' => 9.9, 'inventory' => 8],
            'KC0008' => ['price' => 9.0, 'inventory' => null],
            'KC0009' => ['price' => 9.9, 'inventory' => 99],
            'KC0010' => ['price' => 10.0, 'inventory' => null],
            'KC0011' => ['price' => 11.0, 'inventory' => null],
            'KC0012' => ['price' => 9.9, 'inventory' => 99],
            'KC0013' => ['price' => 13.0, 'inventory' => null],
            'KC0014' => ['price' => 9.9, 'inventory' => 99],
            'KC0015' => ['price' => 9.0, 'inventory' => null],
            'KC0016' => ['price' => 9.9, 'inventory' => 8],
            'KC0017' => ['price' => 13.0, 'inventory' => null],
            'KC0018' => ['price' => 11.0, 'inventory' => null],
            'KC0019' => ['price' => 9.9, 'inventory' => 8],
            'KC0020' => ['price' => 9.9, 'inventory' => 99],
            'KC0021' => ['price' => 9.9, 'inventory' => 99],
            'KC0022' => ['price' => 9.9, 'inventory' => 99],
            'KC0023' => ['price' => 13.0, 'inventory' => null],
            'KC0024' => ['price' => 13.0, 'inventory' => null],
            'KC0025' => ['price' => 9.9, 'inventory' => 99],
            'KC0026' => ['price' => 11.0, 'inventory' => null],
            'KC0027' => ['price' => 13.0, 'inventory' => null],
            'KC0028' => ['price' => 12.0, 'inventory' => null],
            'KC0029' => ['price' => 9.0, 'inventory' => null],
            'KC0030' => ['price' => 13.0, 'inventory' => null],
            'KC0031' => ['price' => 11.0, 'inventory' => null],
            'KC0032' => ['price' => 9.0, 'inventory' => null],
            'KC0033' => ['price' => 12.0, 'inventory' => null],
            'KC0034' => ['price' => 13.0, 'inventory' => null],
            'KC0035' => ['price' => 9.0, 'inventory' => null],
            'KC0036' => ['price' => 9.0, 'inventory' => null],
            'KC0037' => ['price' => 11.0, 'inventory' => null],
            'KC0038' => ['price' => 12.0, 'inventory' => null],
            'KC0039' => ['price' => 13.0, 'inventory' => null],
            'KC0040' => ['price' => 9.9, 'inventory' => 99],
            'KC0041' => ['price' => 9.0, 'inventory' => null],
            'KC0042' => ['price' => 12.0, 'inventory' => null],
            'KC0043' => ['price' => 12.0, 'inventory' => null],
            'KC0044' => ['price' => 11.0, 'inventory' => null],
            'KC0045' => ['price' => 9.0, 'inventory' => null],
            'KC0046' => ['price' => 10.0, 'inventory' => null],
            'KC0047' => ['price' => 9.0, 'inventory' => null],
            'KC0048' => ['price' => 9.9, 'inventory' => null],
            'KC0049' => ['price' => 9.9, 'inventory' => null],
            'KC0050' => ['price' => 12.0, 'inventory' => null],
            'KC0051' => ['price' => 13.0, 'inventory' => null],
            'KC0052' => ['price' => 11.0, 'inventory' => null],
            'KC0053' => ['price' => 9.0, 'inventory' => null],
            'KC0054' => ['price' => 11.0, 'inventory' => null],
            'KC0055' => ['price' => 9.9, 'inventory' => 99],
            'KC0057' => ['price' => 13.0, 'inventory' => null],
            'KC0058' => ['price' => 13.0, 'inventory' => null],
            'KC0059' => ['price' => 11.0, 'inventory' => null],
            'KC0060' => ['price' => 12.0, 'inventory' => null],
            'KC0061' => ['price' => 9.9, 'inventory' => 99],
            'KC0062' => ['price' => 13.0, 'inventory' => null],
            'KC0063' => ['price' => 10.0, 'inventory' => null],
            'KC0064' => ['price' => 9.9, 'inventory' => 99],
            'KC0065' => ['price' => 9.9, 'inventory' => 99],
            'KC0066' => ['price' => 9.0, 'inventory' => null],
            'KC0067' => ['price' => 13.0, 'inventory' => null],
            'KC0068' => ['price' => 9.9, 'inventory' => 99],
            'KC0069' => ['price' => 9.9, 'inventory' => 99],
            'KC0070' => ['price' => 9.9, 'inventory' => 99],
            'KC0071' => ['price' => 9.9, 'inventory' => 99],
            'KC0072' => ['price' => 13.0, 'inventory' => null],
            'KC0073' => ['price' => 13.0, 'inventory' => null],
            'KC0074' => ['price' => 9.9, 'inventory' => 99],
            'KC0075' => ['price' => 9.9, 'inventory' => 99],
            'KC0076' => ['price' => 9.9, 'inventory' => 99],
            'KC0077' => ['price' => 9.9, 'inventory' => 99],
            'KC0078' => ['price' => 9.9, 'inventory' => 8],
            'KC0079' => ['price' => 9.9, 'inventory' => 8],
            'KC0080' => ['price' => 9.9, 'inventory' => 99],
            'KC0081' => ['price' => 9.9, 'inventory' => 8],
            'KC0082' => ['price' => 9.9, 'inventory' => 99],
            'KC0083' => ['price' => 9.9, 'inventory' => 0],
            'KC0084' => ['price' => 13.0, 'inventory' => null],
            'KC0085' => ['price' => 9.9, 'inventory' => 99],
            'KC0086' => ['price' => 9.9, 'inventory' => 8],
            'KC0087' => ['price' => 9.0, 'inventory' => null],
            'KC0088' => ['price' => 9.9, 'inventory' => 8],
            'KC0089' => ['price' => 9.9, 'inventory' => 99],
            'KC0090' => ['price' => 12.0, 'inventory' => null],
            'KC0091' => ['price' => 12.0, 'inventory' => null],
            'KC0092' => ['price' => 12.0, 'inventory' => null],
            'KC0093' => ['price' => 12.0, 'inventory' => null],
            'KC0094' => ['price' => 9.9, 'inventory' => 99],
            'KC0095' => ['price' => 9.9, 'inventory' => 99],
            'KC0096' => ['price' => 13.0, 'inventory' => null],
            'KC0097' => ['price' => 12.0, 'inventory' => null],
            'KC0098' => ['price' => 13.0, 'inventory' => null],
            'KC0099' => ['price' => 13.0, 'inventory' => null],
            'KC0100' => ['price' => 13.0, 'inventory' => null],
            'KC0101' => ['price' => 9.9, 'inventory' => 99],
            'KC0102' => ['price' => 9.9, 'inventory' => 99],
            'KC0103' => ['price' => 12.0, 'inventory' => null],
            'KC0104' => ['price' => 9.9, 'inventory' => 99],
            'KC0105' => ['price' => 9.0, 'inventory' => null],
            'KC0107' => ['price' => 9.0, 'inventory' => 99],
            'KC0108' => ['price' => 13.0, 'inventory' => null],
            'KC0109' => ['price' => 9.0, 'inventory' => null],
            'KC0110' => ['price' => 12.0, 'inventory' => null],
            'KC0111' => ['price' => 13.0, 'inventory' => null],
            'KC0112' => ['price' => 13.0, 'inventory' => null],
            'KC0113' => ['price' => 13.0, 'inventory' => null],
            'KC0114' => ['price' => 13.0, 'inventory' => null],
            'KC0115' => ['price' => 7.9, 'inventory' => 99],
            'KC0116' => ['price' => 9.0, 'inventory' => null],
            'KC0117' => ['price' => 9.9, 'inventory' => 99],
            'KC0118' => ['price' => 9.2, 'inventory' => 99],
            'KC0119' => ['price' => 9.9, 'inventory' => 99],
            'KC0120' => ['price' => 11.0, 'inventory' => null],
            'KC0121' => ['price' => 7.9, 'inventory' => 99],
            'KC0122' => ['price' => 9.9, 'inventory' => 99],
            'KC0123' => ['price' => 9.9, 'inventory' => 99],
            'KC0124' => ['price' => 9.9, 'inventory' => 99],
            'KC0125' => ['price' => 11.0, 'inventory' => null],
            'KC0126' => ['price' => 8.5, 'inventory' => 99],
            'KC0127' => ['price' => 9.0, 'inventory' => null],
            'KC0128' => ['price' => 12.0, 'inventory' => null],
            'KC0129' => ['price' => 12.0, 'inventory' => null],
            'KC0130' => ['price' => 11.0, 'inventory' => null],
            'KC0131' => ['price' => 12.0, 'inventory' => null],
            'KC0132' => ['price' => 11.0, 'inventory' => null],
            'KC0133' => ['price' => 12.0, 'inventory' => null],
            'KC0134' => ['price' => 12.0, 'inventory' => null],
            'KC0136' => ['price' => 13.0, 'inventory' => null],
            'KC0137' => ['price' => 9.9, 'inventory' => 99],
            'KC0138' => ['price' => 8.9, 'inventory' => 99],
            'KC0139' => ['price' => 8.9, 'inventory' => 99],
            'KC0140' => ['price' => 9.9, 'inventory' => 99],
            'KC0141' => ['price' => 9.9, 'inventory' => 99],
            'KC0142' => ['price' => 9.9, 'inventory' => 99],
            'KC0143' => ['price' => 9.9, 'inventory' => 99],
            'KC0144' => ['price' => 13.0, 'inventory' => null],
            'KC0145' => ['price' => 9.9, 'inventory' => 99],
            'KC0146' => ['price' => 11.0, 'inventory' => null],
            'KC0147' => ['price' => 12.0, 'inventory' => null],
            'KC0148' => ['price' => 9.9, 'inventory' => 99],
            'KC0149' => ['price' => 8.9, 'inventory' => 99],
            'KC0150' => ['price' => 8.9, 'inventory' => 99],
            'KC0151' => ['price' => 11.0, 'inventory' => null],
            'KC0152' => ['price' => 9.0, 'inventory' => null],
            'KC0153' => ['price' => 8.9, 'inventory' => 99],
            'KC0154' => ['price' => 9.2, 'inventory' => 99],
            'KC0155' => ['price' => 8.7, 'inventory' => 8],
            'KC0156' => ['price' => 8.6, 'inventory' => 8],
            'KC0157' => ['price' => 9.9, 'inventory' => 99],
            'KC0158' => ['price' => 9.0, 'inventory' => null],
            'KC0159' => ['price' => 9.9, 'inventory' => 99],
            'KC0160' => ['price' => 12.0, 'inventory' => null],
            'KC0161' => ['price' => 11.0, 'inventory' => null],
            'KC0162' => ['price' => 9.2, 'inventory' => 99],
            'KC0163' => ['price' => 6.5, 'inventory' => 8],
            'KC0164' => ['price' => 9.9, 'inventory' => 99],
            'KC0165' => ['price' => 6.5, 'inventory' => 99],
            'KC0166' => ['price' => 9.0, 'inventory' => null],
            'KC0167' => ['price' => 12.0, 'inventory' => null],
            'KC0168' => ['price' => 8.9, 'inventory' => 99],
            'KC0169' => ['price' => 13.0, 'inventory' => null],
            'KC0170' => ['price' => 12.0, 'inventory' => null],
            'KC0171' => ['price' => 13.0, 'inventory' => null],
            'KC0172' => ['price' => 9.2, 'inventory' => 99],
            'KC0173' => ['price' => 8.9, 'inventory' => 99],
            'KC0174' => ['price' => 12.0, 'inventory' => null],
            'KC0175' => ['price' => 11.0, 'inventory' => null],
            'KC0176' => ['price' => 11.0, 'inventory' => null],
            'KC0177' => ['price' => 8.8, 'inventory' => 99],
            'KC0178' => ['price' => 11.0, 'inventory' => null],
            'KC0179' => ['price' => 12.0, 'inventory' => null],
            'KC0180' => ['price' => 10.0, 'inventory' => null],
            'KC0181' => ['price' => 10.0, 'inventory' => null],
            'KC0182' => ['price' => 12.0, 'inventory' => null],
            'KC0183' => ['price' => 12.0, 'inventory' => null],
            'KC0184' => ['price' => 8.8, 'inventory' => 99],
            'KC0185' => ['price' => 8.8, 'inventory' => 99],
            'KC0186' => ['price' => 8.2, 'inventory' => 99],
            'KC0187' => ['price' => 8.8, 'inventory' => 99],
            'KC0188' => ['price' => 11.0, 'inventory' => null],
            'KC0189' => ['price' => 8.8, 'inventory' => 99],
            'KC0190' => ['price' => 8.9, 'inventory' => 99],
            'KC0191' => ['price' => 12.0, 'inventory' => null],
            'KC0192' => ['price' => 8.9, 'inventory' => 99],
            'KC0193' => ['price' => 13.0, 'inventory' => null],
            'KC0194' => ['price' => 11.0, 'inventory' => null],
            'KC0195' => ['price' => 12.0, 'inventory' => null],
            'KC0196' => ['price' => 9.0, 'inventory' => null],
            'KC0197' => ['price' => 12.0, 'inventory' => null],
            'KC0198' => ['price' => 9.9, 'inventory' => 99],
            'KC0199' => ['price' => 12.0, 'inventory' => null],
            'KC0200' => ['price' => 12.0, 'inventory' => null],
            'KC0201' => ['price' => 12.0, 'inventory' => null],
            'KC0202' => ['price' => 8.9, 'inventory' => 99],
            'KC0203' => ['price' => 11.0, 'inventory' => null],
            'KC0204' => ['price' => 11.0, 'inventory' => null],
            'KC0205' => ['price' => 12.0, 'inventory' => null],
            'KC0206' => ['price' => 9.0, 'inventory' => null],
            'KC0207' => ['price' => 8.8, 'inventory' => 99],
            'KC0208' => ['price' => 12.0, 'inventory' => null],
            'KC0209' => ['price' => 12.0, 'inventory' => null],
            'KC0210' => ['price' => 12.0, 'inventory' => null],
            'KC0211' => ['price' => 12.0, 'inventory' => null],
            'KC0212' => ['price' => 12.0, 'inventory' => null],
            'KC0213' => ['price' => 9.9, 'inventory' => 99],
            'KC0214' => ['price' => 13.0, 'inventory' => null],
            'KC0215' => ['price' => 11.0, 'inventory' => null],
            'KC0216' => ['price' => 7.9, 'inventory' => 99],
            'KC0217' => ['price' => 7.9, 'inventory' => 99],
            'KC0218' => ['price' => 7.9, 'inventory' => 99],
            'KC0219' => ['price' => 6.5, 'inventory' => 99],
            'KC0220' => ['price' => 6.5, 'inventory' => 99],
            'KC0221' => ['price' => 8.9, 'inventory' => 99],
            'KC0222' => ['price' => 9.2, 'inventory' => 99],
            'KC0223' => ['price' => 9.9, 'inventory' => 99],
            'KC0224' => ['price' => 8.9, 'inventory' => 99],
            'KC0225' => ['price' => 7.9, 'inventory' => 99],
            'KC0226' => ['price' => 8.9, 'inventory' => 99],
            'KC0227' => ['price' => 13.0, 'inventory' => null],
        ];

        $this->command->info("Loaded " . count($clientProducts) . " products embedded in seeder.");

        // 1. Synchronize Product Prices & Inventory
        $updatedCount = 0;
        $skippedCount = 0;

        foreach ($clientProducts as $sku => $data) {
            $product = Product::where('sku', $sku)->first();
            if (!$product) {
                $skippedCount++;
                continue;
            }

            if (isset($data['price']) && $data['price'] > 0) {
                $product->price = (float) $data['price'];

                if (isset($data['inventory']) && is_numeric($data['inventory'])) {
                    $product->stock = (int) $data['inventory'];
                }

                $product->save();
                $updatedCount++;
            }
        }

        $this->command->info("Successfully updated prices for {$updatedCount} products in the database. Skipped {$skippedCount} unmapped.");

        // 2. Synchronize Official Store Settings
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
