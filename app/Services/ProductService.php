<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    public function index() {
        $products = Product::paginate(10);
        if ($products->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.products_not_found'),
                'data' => [],
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.products_retrieved_successfully'),
            'data' => $products,
        ];
    }
}
