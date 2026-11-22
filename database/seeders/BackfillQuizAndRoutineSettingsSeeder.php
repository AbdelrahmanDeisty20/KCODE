<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Setting;
use App\Services\R479EngineService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BackfillQuizAndRoutineSettingsSeeder extends Seeder
{
    /**
     * Non-destructive, idempotent backfill for ALL Dashboard-controlled Quiz & Post-Quiz Routine data:
     * 1. Quiz Questions & Options (preserving existing IDs & custom Dashboard edits).
     * 2. Global Routine & Diagnosis Settings in `settings` table (SettingResource).
     * 3. Per-product routine usage instructions inside `products.usage_instructions_json` (ProductResource).
     */
    public function run(): void
    {
        $settingsCount = 0;
        $productsCount = 0;

        DB::transaction(function () use (&$settingsCount, &$productsCount) {
            // 1. Safe non-destructive upsert of Quiz Questions & Options
            (new QuizQuestionSeeder())->run();

            // 2. Non-destructive insert/backfill of routine Dashboard settings
            foreach (SettingSeeder::routineDashboardSettings() as $row) {
                $existing = Setting::where('key_en', $row['key_en'])->first();
                if (!$existing) {
                    Setting::create($row);
                    $settingsCount++;
                    continue;
                }

                $dirty = false;
                if (trim((string) $existing->key_ar) === '') {
                    $existing->key_ar = $row['key_ar'];
                    $dirty = true;
                }
                if (trim((string) $existing->value_ar) === '') {
                    $existing->value_ar = $row['value_ar'];
                    $dirty = true;
                }
                if (trim((string) $existing->value_en) === '' || preg_match('/[\x{0600}-\x{06FF}]/u', (string) $existing->value_en)) {
                    $existing->value_en = $row['value_en'];
                    $dirty = true;
                }
                if ($dirty) {
                    $existing->save();
                    $settingsCount++;
                }
            }

            // 3. Non-destructive backfill of per-product routine usage fields inside usage_instructions_json
            $engine = new R479EngineService();
            $defaultsBySku = $engine->getCatalogUsageDefaults();

            Product::whereNotNull('sku')
                ->whereIn('sku', array_keys($defaultsBySku))
                ->orderBy('id')
                ->chunkById(100, function ($products) use ($defaultsBySku, $engine, &$productsCount) {
                    foreach ($products as $product) {
                        $defaults = $defaultsBySku[$product->sku] ?? [];
                        if (empty($defaults)) {
                            continue;
                        }

                        $json = is_array($product->usage_instructions_json) ? $product->usage_instructions_json : [];
                        $dirty = false;

                        foreach ($defaults as $key => $val) {
                            $current = trim((string) ($json[$key] ?? ''));
                            $isEnKey = str_ends_with($key, '_en');
                            $needs = $current === '' || ($isEnKey && $engine->containsArabic($current));

                            if ($needs && $val !== '') {
                                $json[$key] = $val;
                                $dirty = true;
                            }
                        }

                        if ($dirty) {
                            $product->usage_instructions_json = $json;
                            $product->save();
                            $productsCount++;
                        }
                    }
                });
        });

        $this->command?->info("BackfillQuizAndRoutineSettingsSeeder updated {$settingsCount} setting(s) and {$productsCount} product(s).");
    }
}
