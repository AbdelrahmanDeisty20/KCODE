<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Offer;
use Illuminate\Database\Seeder;

class OfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing offers
        Offer::truncate();

        // Get 10 products to add offers for
        $products = Product::limit(10)->get();

        foreach ($products as $index => $product) {
            // Seed a mix of active and inactive offers
            $isActive = $index < 6; // 6 active offers
            $discount = ($index + 1) * 5; // 5%, 10%, 15%, etc.

            Offer::create([
                'product_id' => $product->id,
                'discount_percentage' => $isActive ? $discount : 15.0,
                'start_date' => now()->subDays(5),
                'end_date' => $isActive ? now()->addDays(15) : now()->subDay(1),
                'is_active' => true,
            ]);
        }
    }
}
