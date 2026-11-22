<?php

namespace Database\Seeders;

use App\Models\Product;
use Database\Seeders\Support\R479EnglishBuilder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BackfillEnglishProductPdpSeeder extends Seeder
{
    /**
     * Safely populate missing or Arabic-contaminated English PDP fields on existing products
     * without deleting rows, resetting stock/prices, or overwriting existing custom English text.
     */
    public function run(): void
    {
        $maps = R479EnglishBuilder::loadMetadataMaps();
        $snapshotBySku = $maps['snapshot'];
        $kcodeBySku = $maps['kcode'];

        $updatedCount = 0;

        DB::transaction(function () use ($snapshotBySku, $kcodeBySku, &$updatedCount) {
            Product::with(['brand', 'category', 'subCategory'])
                ->orderBy('id')
                ->chunk(50, function ($products) use ($snapshotBySku, $kcodeBySku, &$updatedCount) {
                    foreach ($products as $product) {
                        if (R479EnglishBuilder::fillMissingEnglishOnModel($product, $snapshotBySku, $kcodeBySku)) {
                            $product->save();
                            $updatedCount++;
                        }
                    }
                });
        });

        $this->command?->info("Safely populated English PDP fields on {$updatedCount} product(s) without modifying Arabic fields.");
    }
}
