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
                  ->orWhere('short_name_en', 'LIKE', '%' . $query . '%')
                  ->orWhere('short_name_ar', 'LIKE', '%' . $query . '%');
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
        $product = Product::with('brand', 'subCategory','reviews','offers','category')->find($id);
        if (!$product) {
            return [
                'status' => false,
                'message' => __('messages.product_not_found'),
                'data' => null,
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

        $productRoutine = \App\Models\ProductRoutine::where('product_id', $product->id)->first();
        $stepId = $productRoutine ? $productRoutine->routine_step_id : null;

        $user = auth('sanctum')->user();
        $skinTypeId = null;
        $concernIds = [];
        if ($user) {
            $assessment = \App\Models\Assessment::where('user_id', $user->id)->with('concerns')->first();
            if ($assessment) {
                $skinTypeId = $assessment->skin_type_id;
                $concernIds = $assessment->concerns->pluck('concern_id')->all();
            }
        }

        $explicit = \App\Models\ProductAlternative::where('product_id', $product->id)
            ->with(['alternative.brand', 'alternative.skinTypes', 'alternative.routines'])
            ->orderBy('priority', 'desc')
            ->get();

        $alternatives = collect();
        foreach ($explicit as $item) {
            $altProd = $item->alternative;
            if ($altProd && $altProd->sales_count >= 100) {
                $alternatives->push($altProd);
            }
        }

        if ($alternatives->count() < 6 && $stepId) {
            $existingIds = $alternatives->pluck('id')->all();
            $existingIds[] = $product->id;

            $query = Product::query()
                ->bestSeller()
                ->whereKeyNot($existingIds)
                ->whereHas('routines', function ($q) use ($stepId) {
                    $q->where('routine_step_id', $stepId);
                });

            if ($skinTypeId) {
                $query->whereHas('skinTypes', function ($q) use ($skinTypeId) {
                    $q->where('skin_type_id', $skinTypeId);
                });
            }

            // Prioritize products matching user's concerns
            if (!empty($concernIds)) {
                $queryClone = clone $query;
                $queryClone->whereHas('concerns', function ($q) use ($concernIds) {
                    $q->whereIn('concern_id', $concernIds);
                });

                $fallbacks = $queryClone->with(['brand', 'skinTypes', 'routines'])
                    ->limit(6 - $alternatives->count())
                    ->get();

                foreach ($fallbacks as $fallback) {
                    $alternatives->push($fallback);
                }
            }

            // If still less than 6, get fallback products for the step without concern filter
            if ($alternatives->count() < 6) {
                $existingIds = $alternatives->pluck('id')->all();
                $existingIds[] = $product->id;

                $query2 = Product::query()
                    ->bestSeller()
                    ->whereKeyNot($existingIds)
                    ->whereHas('routines', function ($q) use ($stepId) {
                        $q->where('routine_step_id', $stepId);
                    });

                if ($skinTypeId) {
                    $query2->whereHas('skinTypes', function ($q) use ($skinTypeId) {
                        $q->where('skin_type_id', $skinTypeId);
                    });
                }

                $fallbacks2 = $query2->with(['brand', 'skinTypes', 'routines'])
                    ->limit(6 - $alternatives->count())
                    ->get();

                foreach ($fallbacks2 as $f) {
                    $alternatives->push($f);
                }
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
}
