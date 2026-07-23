<?php

namespace App\Services;

use App\Http\Resources\API\CATEGORY\SkinTypeResource;
use App\Models\Product;
use App\Models\SkinType;

class SkinService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        
    }
    public function SkinTypes() {
        $skinTypes = SkinType::paginate(10);
        if ($skinTypes->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.no_skin_types_found'),
                'data' => [],
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.skin_types_retrieved_successfully'),
            'data' => $skinTypes,
        ];
    }
    public function show($id) {
        $products = Product::whereHas('skinTypes', function ($q) use ($id) {
            $q->where('skin_type_id', $id);
        })->with(['brand', 'offers','skin'])->paginate(10);

        if ($products->isEmpty()) {
            return [
                'status'  => false,
                'message' => __('messages.products_not_found'),
                'data'    => [],
            ];
        }

        return [
            'status'  => true,
            'message' => __('messages.skin_type_retrieved_successfully'),
            'data'    => $products,
        ];
    }
}
