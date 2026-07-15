<?php

namespace App\Services;

use App\Models\Category;
use App\Models\SubCategory;

class CategoryService
{
    public function index()
    {
        $categories = Category::paginate(10);
        if ($categories->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.no_categories_found'),
                'data' => [],
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.categories_retrieved_successfully'),
            'data' => $categories,
        ];
    }

    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return [
                'status' => false,
                'message' => __('messages.category_not_found'),
                'data' => null,
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.category_retrieved_successfully'),
            'data' => $category,
        ];
    }
    public function sub_categories()
    {
        $sub_categories = SubCategory::with('products')->paginate(10);
        if ($sub_categories->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.sub_categories_not_found'),
                'data' => [],
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.sub_categories_retrieved_successfully'),
            'data' => $sub_categories,
        ];
    }
}
