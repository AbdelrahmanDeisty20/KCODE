<?php

namespace App\Services;

use App\Http\Resources\API\BRAND\BrandResource;
use App\Models\Brand;

class BrandService
{
    public function index()
    {
        $brands = Brand::paginate(10);
        if ($brands->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.brandsNotFound'),
                'data' => [],
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.brandsGetSuccessfully'),
            'data' => $brands,
        ];
    }
    public function show($id)
    {
        $brand = Brand::with('products')->find($id);
        if (!$brand) {
            return [
                'status' => false,
                'message' => __('messages.brandNotFound'),
                'data' => [],
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.brandGetSuccessfully'),
            'data' => $brand,
        ];
    }
}
