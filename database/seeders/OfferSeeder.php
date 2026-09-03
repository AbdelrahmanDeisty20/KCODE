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

        // Get 20 different products to add active offers for
        $products = Product::where('stock', '>', 0)->inRandomOrder()->limit(20)->get();

        if ($products->count() < 20) {
            $products = Product::limit(20)->get();
        }

        $discounts = [10, 15, 20, 25, 30, 35, 40, 45, 50];

        foreach ($products as $index => $product) {
            $discount = $discounts[$index % count($discounts)];

            Offer::create([
                'product_id' => $product->id,
                'discount_percentage' => $discount,
                'start_date' => now()->subDays(rand(1, 10)),
                'end_date' => now()->addDays(rand(15, 45)),
                'is_active' => true,
            ]);
        }
    }
}
