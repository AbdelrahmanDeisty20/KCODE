<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    public function index() {
        $products = Product::with('brand', 'subCategory','offers','category')
            ->paginate(10);
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

    public function search(array $data) {
        $query = $data['query'];

        $products = Product::with(['brand', 'subCategory', 'offers'])
            ->where(function ($q) use ($query) {
                $q->where('name_en', 'LIKE', '%' . $query . '%')
                  ->orWhere('name_ar', 'LIKE', '%' . $query . '%')
                  ->orWhere('sku', 'LIKE', '%' . $query . '%')
                  ->orWhere('description_en', 'LIKE', '%' . $query . '%')
                  ->orWhere('description_ar', 'LIKE', '%' . $query . '%')
                  ->orWhere('short_name_en', 'LIKE', '%' . $query . '%')
                  ->orWhere('short_name_ar', 'LIKE', '%' . $query . '%')
                  ->orWhereHas('brand', function ($q) use ($query) {
                      $q->where('name_en', 'LIKE', '%' . $query . '%')
                        ->orWhere('name_ar', 'LIKE', '%' . $query . '%');
                  })
                  ->orWhereHas('subCategory', function ($q) use ($query) {
                      $q->where('name_en', 'LIKE', '%' . $query . '%')
                        ->orWhere('name_ar', 'LIKE', '%' . $query . '%');
                  });
            })
            ->paginate(10);

        if ($products->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.products_not_found'),
                'data' => $products,
            ];
        }

        return [
            'status' => true,
            'message' => __('messages.products_retrieved_successfully'),
            'data' => $products,
        ];
    }

    public function filter(array $filters = []) {
        $query = Product::with(['brand', 'subCategory', 'reviews','offers','category']);

        // 1. Category Filter
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // 2. SubCategory Filter
        if (!empty($filters['sub_category_id'])) {
            $query->where('sub_category_id', $filters['sub_category_id']);
        }

        // 3. Brand Filter
        if (!empty($filters['brand_id'])) {
            if (is_array($filters['brand_id'])) {
                $query->whereIn('brand_id', $filters['brand_id']);
            } else {
                $query->where('brand_id', $filters['brand_id']);
            }
        }

        // 4. Skin Type Filter
        if (!empty($filters['skin_type_id'])) {
            $skinTypeIds = is_array($filters['skin_type_id']) ? $filters['skin_type_id'] : [$filters['skin_type_id']];
            $query->whereHas('skinTypes', function ($q) use ($skinTypeIds) {
                $q->whereIn('skin_type_id', $skinTypeIds);
            });
        }

        // 5. Goal Filter
        if (!empty($filters['goal_id'])) {
            $goalIds = is_array($filters['goal_id']) ? $filters['goal_id'] : [$filters['goal_id']];
            $query->whereHas('goals', function ($q) use ($goalIds) {
                $q->whereIn('goal_id', $goalIds);
            });
        }

        // 5b. Concern Filter
        if (!empty($filters['concern_id'])) {
            $concernIds = is_array($filters['concern_id']) ? $filters['concern_id'] : [$filters['concern_id']];
            $query->whereHas('concerns', function ($q) use ($concernIds) {
                $q->whereIn('concern_id', $concernIds);
            });
        }

        // 6. Best Seller Filter
        if (isset($filters['is_best_seller'])) {
            $query->where('is_best_seller', (bool)$filters['is_best_seller']);
        }

        // 7. Price Range Filter
        if (isset($filters['min_price']) || isset($filters['max_price'])) {
            $min = $filters['min_price'] ?? 0;
            $max = $filters['max_price'] ?? 999999;
            $query->whereBetween('price', [$min, $max]);
        }

        // 8. Ratings Filter
        if (!empty($filters['ratings'])) {
            $query->withAvg('reviews', 'rating')
                ->having('reviews_avg_rating', '>=', $filters['ratings']);
        }

        // 9. Sorting
        $sort = $filters['sort'] ?? 'latest';

        // Load average rating if sorted by ratings
        if ($sort === 'ratings' && empty($filters['ratings'])) {
            $query->withAvg('reviews', 'rating');
        }

        switch ($sort) {
            case 'min_price':
                $query->orderBy('price', 'asc');
                break;
            case 'max_price':
                $query->orderBy('price', 'desc');
                break;
            case 'best_sellers':
            case 'sales_count':
                $query->orderBy('sales_count', 'desc');
                break;
            case 'ratings':
                $query->orderByRaw('COALESCE(reviews_avg_rating, 0) desc');
                break;
            case 'latest':
            default:
                $query->orderByDesc('id');
                break;
        }

        $perPage = $filters['per_page'] ?? 10;
        $products = $query->paginate($perPage);

        if ($products->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.products_not_found'),
                'data' => $products,
            ];
        }

        return [
            'status' => true,
            'message' => __('messages.products_retrieved_successfully'),
            'data' => $products,
        ];
    }
    public function show($id) {
        $product = Product::with([
            'brand', 
            'subCategory', 
            'reviews' => function ($query) {
                $query->orderBy('id', 'desc');
            }, 
            'reviews.user', 
            'offers', 
            'category', 
            'images'
        ])->find($id);
        if (!$product) {
            return [
                'status' => false,
                'message' => __('messages.product_not_found'),
                'data' => [],
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.product_retrieved_successfully'),
            'data' => $product,
        ];
    }

    public function alternatives($id) {
        $product = Product::with('brand', 'subCategory','offers')->find($id);
        if (!$product) {
            return [
                'status' => false,
                'message' => __('messages.product_not_found'),
                'data' => collect(),
            ];
        }

        // Check if there are explicit alternatives in the database
        $explicitCount = \App\Models\ProductAlternative::where('product_id', $product->id)->count();

        if ($explicitCount === 0) {
            // Generate them automatically in the database at this exact moment!
            $this->generateAlternatives($product->id);
        }

        // Now load them from the database
        $explicit = \App\Models\ProductAlternative::where('product_id', $product->id)
            ->with(['alternative.brand', 'alternative.skinTypes', 'alternative.routines'])
            ->orderBy('priority', 'desc')
            ->get();

        $alternatives = collect();
        foreach ($explicit as $item) {
            $altProd = $item->alternative;
            if ($altProd) {
                $alternatives->push($altProd);
            }
        }

        // Return up to 6 products
        return [
            'status' => true,
            'message' => __('messages.alternatives_retrieved_successfully'),
            'data' => $alternatives->take(6),
        ];
    }

    public function bestSellers() {
        $products = Product::bestSeller()
            ->with(['brand', 'subCategory'])
            ->orderBy('sales_count', 'desc')
            ->paginate(10);

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

    public function bySkinType($skinTypeId) {
        // Verify skin type exists
        if (!\App\Models\SkinType::where('id', $skinTypeId)->exists()) {
            return [
                'status' => false,
                'message' => __('messages.invalid_skin_type'),
                'data' => [],
            ];
        }

        $products = Product::whereHas('skinTypes', function ($q) use ($skinTypeId) {
            $q->where('skin_type_id', $skinTypeId);
        })
        ->with(['brand', 'subCategory'])
        ->paginate(10);

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

    public function byGoal($goalId) {
        // Verify goal exists
        if (!\App\Models\RoutineGoal::where('id', $goalId)->exists()) {
            return [
                'status' => false,
                'message' => __('messages.invalid_routine_goal'),
                'data' => [],
            ];
        }

        $products = Product::whereHas('goals', function ($q) use ($goalId) {
            $q->where('goal_id', $goalId);
        })
        ->with(['brand', 'subCategory'])
        ->paginate(10);

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

    public function generateAlternatives($productId = null) {
        if ($productId) {
            $products = Product::where('id', $productId)->get();
        } else {
            $products = Product::all();
        }

        $count = 0;
        foreach ($products as $product) {
            $productRoutine = \App\Models\ProductRoutine::where('product_id', $product->id)->first();
            $stepId = $productRoutine ? $productRoutine->routine_step_id : null;

            if (!$stepId) {
                continue;
            }

            // Find other products belonging to the same step
            $otherProducts = Product::where('id', '!=', $product->id)
                ->whereHas('routines', function ($q) use ($stepId) {
                    $q->where('routine_step_id', $stepId);
                })
                ->orderByDesc('sales_count')
                ->get();

            foreach ($otherProducts as $altProd) {
                $exists = \App\Models\ProductAlternative::where('product_id', $product->id)
                    ->where('alternative_id', $altProd->id)
                    ->exists();

                if (!$exists) {
                    \App\Models\ProductAlternative::create([
                        'product_id' => $product->id,
                        'alternative_id' => $altProd->id,
                        'priority' => 0,
                        'reason_ar' => 'بديل تلقائي',
                        'reason_en' => 'Auto-generated alternative',
                    ]);
                    $count++;
                }
            }
        }

        return [
            'status' => true,
            'message' => "Successfully generated {$count} new alternatives in the database.",
            'data' => $count
        ];
    }
}
