<?php

namespace App\Services;

use App\Models\Product;

class OfferService
{
    /**
     * Get products that have active offers.
     */
    public function getOfferProducts()
    {
        $products = Product::whereHas('offers', function ($query) {
            $query->active();
        })
        ->with(['brand', 'subCategory', 'offers'])
        ->paginate(10);

        if ($products->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.no_offers_found'),
                'data' => $products,
            ];
        }

        return [
            'status' => true,
            'message' => __('messages.offers_retrieved_successfully'),
            'data' => $products,
        ];
    }
}
