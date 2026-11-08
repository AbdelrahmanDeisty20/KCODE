<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SyncAllR479ProductsSeeder extends Seeder
{
    /**
     * Synchronize all 256 products with the client-approved R479 canonical files:
     * - database/seeders/data/r479_products.json
     * - database/seeders/data/r479_compact_pages.json
     * - database/seeders/data/r479_master_snapshot.json
     */
    public function run(): void
    {
        $productsJsonPath = base_path('database/seeders/data/r479_products.json');
        $pagesJsonPath = base_path('database/seeders/data/r479_compact_pages.json');
        $snapshotJsonPath = base_path('database/seeders/data/r479_master_snapshot.json');

        if (!File::exists($productsJsonPath) || !File::exists($pagesJsonPath)) {
            $this->command->error("R479 canonical data files not found in database/seeders/data/");
            return;
        }

        $this->command->info("📖 Loading R479 canonical data files...");
        $productsData = json_decode(File::get($productsJsonPath), true) ?? [];
        $pagesData = json_decode(File::get($pagesJsonPath), true) ?? [];
        $snapshotData = File::exists($snapshotJsonPath) ? (json_decode(File::get($snapshotJsonPath), true) ?? []) : [];

        $snapshotBySku = [];
        foreach ($snapshotData as $item) {
            if (!empty($item['SKU'])) {
                $snapshotBySku[$item['SKU']] = $item;
            }
        }

        $this->command->info("Found " . count($productsData) . " products in R479 dictionary.");

        $updatedCount = 0;
        $missingCount = 0;

        DB::transaction(function () use ($productsData, $pagesData, $snapshotBySku, &$updatedCount, &$missingCount) {
            foreach ($productsData as $sku => $p) {
                $product = Product::where('sku', $sku)->first();
                if (!$product) {
                    $missingCount++;
                    continue;
                }

                $html = $pagesData[$sku] ?? '';
                $snap = $snapshotBySku[$sku] ?? [];

                // 1. Clean product names and descriptions
                $nameClean = trim($p['name'] ?? $product->name_en);
                $briefClean = trim($p['brief'] ?? $product->short_name_ar);
                $descClean = trim($p['description'] ?? $product->description_ar);
                $size = trim($p['size'] ?? ($product->size ?: ($snap['Size'] ?? '')));
                $barcode = trim($p['barcode'] ?? ($product->barcode ?: ($snap['Barcode'] ?? '')));
                $price = !empty($p['price']) ? (float)$p['price'] : ((float)$product->price ?: (float)($snap['Price'] ?? 0));

                // 2. Specifications from compact_pages.json
                $specsSection = $this->extractBetween($html, 'class="cq-kr-spec-list">', '</dl>');
                $specEntries = [];
                if (preg_match_all('/<dt>([^<]+)<\/dt>\s*<dd><bdi[^>]*>([^<]+)<\/bdi><\/dd>/u', $specsSection, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $row) {
                        $specEntries[trim($row[1])] = trim($row[2]);
                    }
                }

                $roleAr = $specEntries['النوع'] ?? ($snap['Category'] ?? $product->role_ar);
                $textureAr = $specEntries['القوام'] ?? ($snap['Texture'] ?? $product->texture_ar);
                $textureEn = $snap['Texture'] ?? ($product->texture_en ?: 'Smooth');
                $fragrance = $specEntries['العطر'] ?? (($snap['Fragrance'] ?? '') === 'None' ? 'دون عطر مضاف أو زيوت عطرية' : ($snap['Fragrance'] ?? 'دون عطر مضاف'));

                // 3. Full INCI Ingredients from compact_pages.json
                $inciHtml = $this->extractBetween($html, 'class="cq-kr-inci"', '</p>');
                $inci = $this->cleanHtml($inciHtml);
                $inci = trim(preg_replace('/^[^>]*>/', '', $inci));
                if (empty($inci)) {
                    $inci = $product->ingredients_ar ?: ($product->ingredients_en ?: '');
                }

                // 4. FAQs from compact_pages.json (4 questions)
                $faqs = [];
                $faqSection = $this->extractBetween($html, 'class="cq-pfaq-list">', '</section>');
                if (preg_match_all('/<details><summary><span>([^<]+)<\/span>[\s\S]*?<div class="cq-pfaq-answer"><p>([\s\S]*?)<\/p><\/div><\/details>/u', $faqSection, $m, PREG_SET_ORDER)) {
                    $idx = 1;
                    foreach ($m as $row) {
                        $faqs[] = [
                            'id' => 'faq_' . $idx++,
                            'question' => trim($row[1]),
                            'answer' => $this->cleanHtml($row[2])
                        ];
                    }
                }
                if (empty($faqs) && !empty($product->product_faqs_json)) {
                    $faqs = $product->product_faqs_json;
                }

                // 5. Verdict & Sensitive fit from summaryHtml
                $summaryHtml = $p['summaryHtml'] ?? '';
                $verdictNote = $this->cleanHtml($this->extractBetween($summaryHtml, 'class="verdict-note master-copy">', '</p>'));
                if (empty($verdictNote)) {
                    $verdictNote = $descClean;
                }

                $fitValue = $this->cleanHtml($this->extractBetween($summaryHtml, '<strong class="fit-value">', '</strong>'));
                if (empty($fitValue)) {
                    $fitValue = 'الدهنية · المختلطة · العادية · الجافة';
                }

                $fitGuidance = $this->cleanHtml($this->extractBetween($summaryHtml, '<p class="fit-guidance">', '</p>'));
                $fitStatus = $this->cleanHtml($this->extractBetween($summaryHtml, '<strong class="fit-value fit-status">', '</strong>'));
                if (empty($fitStatus)) {
                    $fitStatus = $snap['Sensitive Classification'] ?? 'مناسب للبشرة الحساسة';
                }

                $sensitiveSection = $this->extractBetween($summaryHtml, 'class="fit-item fit-sensitive"', '</section>');
                $sensitiveGuidance = $this->cleanHtml($this->extractBetween($sensitiveSection, '<p class="fit-guidance">', '</p>'));
                if (empty($sensitiveGuidance)) {
                    $sensitiveGuidance = $fitGuidance;
                }

                $isSensitiveEligible = ($fitStatus === 'مناسب للبشرة الحساسة' || ($snap['Sensitive Classification'] ?? '') === 'مناسب للبشرة الحساسة');

                // 6. Usage Tiles & Benefits
                $usageTiles = $p['usage_tiles'] ?? [];
                $benefits = $p['benefits'] ?? [];

                $t0 = $usageTiles[0]['text'] ?? ($usageTiles[0]['value'] ?? 'صباحًا ومساءً يوميًا');
                $t1 = $usageTiles[1]['text'] ?? ($usageTiles[1]['value'] ?? 'كمية مناسبة');
                $t2 = $usageTiles[2]['text'] ?? ($usageTiles[2]['value'] ?? 'ربّت بلطف');
                $t3 = $usageTiles[3]['text'] ?? ($usageTiles[3]['value'] ?? 'اتبع بمرطب وواقي شمس');

                $usageInstructions = [
                    'timing_label' => $usageTiles[0]['label'] ?? 'التوقيت',
                    'timing' => $t0,
                    'timing_note' => $usageTiles[0]['note'] ?? '',
                    'amount_label' => $usageTiles[1]['label'] ?? 'الكمية',
                    'amount' => $t1,
                    'amount_note' => $usageTiles[1]['note'] ?? '',
                    'application_label' => $usageTiles[2]['label'] ?? 'طريقة الوضع',
                    'application_method' => $t2,
                    'application_note' => $usageTiles[2]['note'] ?? '',
                    'gradual_start_label' => $usageTiles[3]['label'] ?? 'الحماية والتثبيت',
                    'gradual_start' => $t3,
                    'gradual_start_note' => $usageTiles[3]['note'] ?? '',
                    'caution' => 'يحفظ في مكان بارد وجاف بعيدًا عن أشعة الشمس المباشرة. يُنصح بإجراء اختبار حساسية قبل الاستخدام الكامل.',
                    'tiles' => $usageTiles,
                    'benefits' => $benefits,
                    'verdict' => [
                        'note' => $verdictNote,
                        'skin_fit_value' => $fitValue,
                        'skin_fit_guidance' => $fitGuidance,
                        'sensitive_status' => $fitStatus,
                        'sensitive_guidance' => $sensitiveGuidance,
                        'fragrance' => $fragrance,
                        'summary_html' => $summaryHtml,
                    ],
                ];

                // 7. Apply updates to product
                $product->name_en = $nameClean;
                $product->name_ar = $nameClean;
                $product->short_name_en = $nameClean;
                $product->short_name_ar = $briefClean;
                $product->brief_insight_ar = $briefClean;
                $product->description_ar = $descClean;
                $product->description_en = $descClean;
                $product->why_kcode_ar = $descClean;
                $product->size = $size;
                $product->barcode = $barcode;
                if ($price > 0) {
                    $product->price = $price;
                }
                $product->role_ar = $roleAr;
                $product->texture_ar = $textureAr;
                $product->texture_en = $textureEn;
                $product->ingredients_ar = $inci;
                $product->ingredients_en = $inci;
                $product->how_to_use_ar = $p['usage'] ?? $product->how_to_use_ar;
                $product->how_to_use_en = $p['usage'] ?? $product->how_to_use_en;
                $product->ar_key_benefits = $benefits;
                $product->product_faqs_json = $faqs;
                $product->usage_instructions_json = $usageInstructions;
                $product->sensitive_eligible = $isSensitiveEligible;
                $product->image = "products/{$sku}.webp";

                $product->save();
                $updatedCount++;
            }
        });

        $this->command->info("🎉 Successfully synced {$updatedCount} products with R479 canonical files! (Missing SKUs in DB: {$missingCount})");
    }

    private function extractBetween(?string $str, string $start, string $end): string
    {
        if (empty($str)) return '';
        $s = strpos($str, $start);
        if ($s === false) return '';
        $s += strlen($start);
        $e = strpos($str, $end, $s);
        if ($e === false) return '';
        return trim(substr($str, $s, $e - $s));
    }

    private function cleanHtml(?string $html): string
    {
        if (empty($html)) return '';
        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return trim(preg_replace('/\s+/u', ' ', $text));
    }
}

