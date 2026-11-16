<?php

namespace App\Services;

use App\Models\Product;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    public function index(?string $searchTerm = null) {
        $searchTerm = $searchTerm ?? request('search') ?? request('q');
        $perPage = (int) request('per_page', 10);
        if ($perPage < 1) $perPage = 10;
        if ($perPage > 500) $perPage = 500;

        $products = Product::where('stock', '>', 0)
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->with([
                'brand',
                'subCategory',
                'category',
                'offers' => function ($q) {
                    $q->active();
                }
            ])
            ->when(request()->filled('skus'), function ($q) {
                $skus = array_filter(array_map('trim', explode(',', request('skus'))));
                if (!empty($skus)) {
                    $q->whereIn('sku', $skus);
                    $skuList = "'" . implode("','", $skus) . "'";
                    $q->orderByRaw("FIELD(sku, {$skuList})");
                }
            })
            ->when(request()->boolean('picks') || request()->has('picks'), function ($q) {
                $picks = ['KC0185', 'KC0137', 'KC0001', 'KC0078', 'KC0118', 'KC0105', 'KC0089', 'KC0159'];
                $q->whereIn('sku', $picks);
                $skuList = "'" . implode("','", $picks) . "'";
                $q->orderByRaw("FIELD(sku, {$skuList})");
            })
            ->when(!empty($searchTerm), function ($q) use ($searchTerm) {
                $terms = [$searchTerm];
                if (str_contains($searchTerm, 'دكتور') || str_contains($searchTerm, 'د.')) {
                    $terms[] = 'Dr.';
                    $terms[] = 'Dr';
                    $terms[] = 'Doctor';
                }
                $q->where(function ($sub) use ($terms) {
                    foreach ($terms as $term) {
                        $sub->orWhere('name_en', 'LIKE', "%{$term}%")
                            ->orWhere('name_ar', 'LIKE', "%{$term}%")
                            ->orWhere('sku', 'LIKE', "%{$term}%")
                            ->orWhereHas('brand', fn($b) => $b->where('name_ar', 'LIKE', "%{$term}%")->orWhere('name_en', 'LIKE', "%{$term}%"));
                    }
                });
            })
            ->paginate($perPage);

        return [
            'status' => true,
            'message' => __('messages.products_retrieved_successfully'),
            'data' => $products,
        ];
    }

    public function search(array $data) {
        $query = trim($data['query'] ?? request('query') ?? request('search') ?? request('q') ?? '');
        $perPage = (int) ($data['per_page'] ?? request('per_page', 10));
        if ($perPage < 1) $perPage = 10;
        if ($perPage > 100) $perPage = 100;

        if (!empty($query)) {
            $user = auth('sanctum')->user() ?? auth()->user();
            $clientName = $user?->name ?? 'زائر المتجر';
            ActivityLogger::log(
                event: 'search',
                description: "قام العميل/الزائر [{$clientName}] بالبحث في المتجر عن: \"{$query}\"",
                subjectType: 'Search',
                subjectId: null,
                newValues: ['query' => $query],
                userId: $user?->id,
                userName: $clientName
            );
        }

        $terms = [$query];
        if (str_contains($query, 'دكتور') || str_contains($query, 'د.')) {
            $terms[] = 'Dr.';
            $terms[] = 'Dr';
            $terms[] = 'Doctor';
        }

        $products = Product::where('stock', '>', 0)
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->with([
                'brand',
                'subCategory',
                'category',
                'concerns',
                'skinTypes',
                'goals',
                'offers' => function ($q) {
                    $q->active();
                }
            ])
            ->when(!empty($query), function ($q) use ($terms) {
                $q->where(function ($topQ) use ($terms) {
                    foreach ($terms as $term) {
                        $topQ->orWhere(function ($sub) use ($term) {
                            $sub->where('name_en', 'LIKE', '%' . $term . '%')
                              ->orWhere('name_ar', 'LIKE', '%' . $term . '%')
                              ->orWhere('sku', 'LIKE', '%' . $term . '%')
                              ->orWhere('description_en', 'LIKE', '%' . $term . '%')
                              ->orWhere('description_ar', 'LIKE', '%' . $term . '%')
                              ->orWhere('short_name_en', 'LIKE', '%' . $term . '%')
                              ->orWhere('short_name_ar', 'LIKE', '%' . $term . '%')
                              ->orWhereHas('brand', function ($b) use ($term) {
                                  $b->where('name_en', 'LIKE', '%' . $term . '%')
                                    ->orWhere('name_ar', 'LIKE', '%' . $term . '%');
                              })
                              ->orWhereHas('category', function ($c) use ($term) {
                                  $c->where('name_en', 'LIKE', '%' . $term . '%')
                                    ->orWhere('name_ar', 'LIKE', '%' . $term . '%');
                              })
                              ->orWhereHas('subCategory', function ($sc) use ($term) {
                                  $sc->where('name_en', 'LIKE', '%' . $term . '%')
                                    ->orWhere('name_ar', 'LIKE', '%' . $term . '%');
                              })
                              ->orWhereHas('concerns', function ($cn) use ($term) {
                                  $cn->where('name_en', 'LIKE', '%' . $term . '%')
                                    ->orWhere('name_ar', 'LIKE', '%' . $term . '%')
                                    ->orWhere('description_en', 'LIKE', '%' . $term . '%')
                                    ->orWhere('description_ar', 'LIKE', '%' . $term . '%');
                              })
                              ->orWhereHas('skinTypes', function ($st) use ($term) {
                                  $st->where('name_en', 'LIKE', '%' . $term . '%')
                                    ->orWhere('name_ar', 'LIKE', '%' . $term . '%')
                                    ->orWhere('description_en', 'LIKE', '%' . $term . '%')
                                    ->orWhere('description_ar', 'LIKE', '%' . $term . '%');
                              })
                              ->orWhereHas('goals', function ($g) use ($term) {
                                  $g->where('name_en', 'LIKE', '%' . $term . '%')
                                    ->orWhere('name_ar', 'LIKE', '%' . $term . '%');
                              });
                        });
                    }
                });
            })
            ->when(!empty($data['skin_type_id']), function ($q) use ($data) {
                $skinTypeIds = is_array($data['skin_type_id']) ? $data['skin_type_id'] : [$data['skin_type_id']];
                $q->whereHas('skinTypes', fn ($skQ) => $skQ->whereIn('skin_types.id', $skinTypeIds));
            })
            ->when(!empty($data['concern_id']), function ($q) use ($data) {
                $concernIds = is_array($data['concern_id']) ? $data['concern_id'] : [$data['concern_id']];
                $q->whereHas('concerns', fn ($cQ) => $cQ->whereIn('concerns.id', $concernIds));
            })
            ->when(!empty($data['goal_id']), function ($q) use ($data) {
                $goalIds = is_array($data['goal_id']) ? $data['goal_id'] : [$data['goal_id']];
                $q->whereHas('goals', fn ($gQ) => $gQ->whereIn('routine_goals.id', $goalIds));
            })
            ->paginate($perPage);

        return [
            'status' => true,
            'message' => $products->isEmpty() ? __('messages.products_not_found') : __('messages.products_retrieved_successfully'),
            'data' => $products,
        ];
    }

    public function filter(array $filters = []) {
        $query = Product::where('stock', '>', 0)
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->with([
                'brand',
                'subCategory',
                'reviews',
                'category',
                'offers' => function ($q) {
                    $q->active();
                }
            ]);

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
                $q->whereIn('skin_types.id', $skinTypeIds);
            });
        }

        // 5. Goal Filter
        if (!empty($filters['goal_id'])) {
            $goalIds = is_array($filters['goal_id']) ? $filters['goal_id'] : [$filters['goal_id']];
            $query->whereHas('goals', function ($q) use ($goalIds) {
                $q->whereIn('routine_goals.id', $goalIds);
            });
        }

        // 5b. Concern Filter
        if (!empty($filters['concern_id'])) {
            $concernIds = is_array($filters['concern_id']) ? $filters['concern_id'] : [$filters['concern_id']];
            $query->whereHas('concerns', function ($q) use ($concernIds) {
                $q->whereIn('concerns.id', $concernIds);
            });
        }

        // 5c. Sensitive Skin Filter (AND with skin type). Only applied when "Yes".
        if (!empty($filters['sensitive_eligible']) && filter_var($filters['sensitive_eligible'], FILTER_VALIDATE_BOOLEAN)) {
            $query->where('sensitive_eligible', true);
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

        return [
            'status' => true,
            'message' => $products->isEmpty() ? __('messages.products_not_found') : __('messages.products_retrieved_successfully'),
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
            'offers' => function ($q) {
                $q->active();
            }, 
            'category', 
            'images',
            'skinTypes',
            'concerns',
            'marketingDetail',
            'recommendationRule'
        ])->find($id);
        if (!$product) {
            return [
                'status' => false,
                'message' => __('messages.product_not_found'),
                'data' => [],
            ];
        }

        // Log product view with 15-minute throttle per user/IP
        try {
            $user = auth('sanctum')->user() ?? auth()->user();
            $clientName = $user?->name ?? 'زائر المتجر';
            $userIp = request()->ip() ?? 'unknown';
            $identifier = $user ? "u_{$user->id}" : "ip_" . md5($userIp);
            $cacheKey = "view_p_{$product->id}_{$identifier}";

            if (!Cache::has($cacheKey)) {
                Cache::put($cacheKey, true, now()->addMinutes(15));
                $pName = $product->name_ar ?: ($product->name_en ?: 'منتج');
                ActivityLogger::log(
                    event: 'product_view',
                    description: "قام العميل/الزائر [{$clientName}] بمشاهدة المنتج [{$pName}] ({$product->sku})",
                    subjectType: 'Product',
                    subjectId: $product->id,
                    newValues: ['sku' => $product->sku, 'price' => $product->price],
                    userId: $user?->id,
                    userName: $clientName
                );
            }
        } catch (\Throwable $e) {
            // Silently fail view log to never block response
        }

        return [
            'status' => true,
            'message' => __('messages.product_retrieved_successfully'),
            'data' => $product,
        ];
    }

    public function alternatives($id) {
        $product = Product::with([
            'brand',
            'subCategory',
            'offers' => function ($q) {
                $q->active();
            }
        ])->find($id);
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
            if ($altProd && $altProd->stock > 0) {
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
        $approvedSkus = [
            'KC0185', // BIODANCE Bio Collagen Real Deep Mask
            'KC0137', // TORRIDEN DIVE IN Low Molecular Hyaluronic Acid Serum
            'KC0001', // ANUA Niacinamide 10 TXA 4 Serum
            'KC0078', // MEDICUBE Zero Pore Pad 2.0
            'KC0118', // BEAUTY OF JOSEON Relief Sun Rice Probiotics SPF50+ PA++++
            'KC0105', // SKIN1004 Madagascar Centella Light Cleansing Oil
            'KC0089', // Dr.Althea 345 Relief Cream
            'KC0159', // COSRX Ultra Light Invisible Sunscreen SPF50 PA++++
        ];

        $skuOrderList = "'" . implode("','", $approvedSkus) . "'";
        $perPage = (int) request('per_page', 8);
        if ($perPage < 1) $perPage = 8;
        if ($perPage > 50) $perPage = 50;

        $products = Product::where('stock', '>', 0)
            ->where(function ($q) use ($approvedSkus) {
                $q->whereIn('sku', $approvedSkus)
                  ->orWhere('is_best_seller', true);
            })
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->with([
                'brand',
                'subCategory',
                'category',
                'offers' => function ($q) {
                    $q->active();
                }
            ])
            ->orderByRaw("CASE WHEN sku IN ({$skuOrderList}) THEN FIELD(sku, {$skuOrderList}) ELSE 99 END")
            ->orderBy('sales_count', 'desc')
            ->paginate($perPage);

        if ($products->isEmpty()) {
            $products = Product::where('stock', '>', 0)
                ->bestSeller()
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->with([
                    'brand',
                    'subCategory',
                    'category',
                    'offers' => function ($q) {
                        $q->active();
                    }
                ])
                ->orderBy('sales_count', 'desc')
                ->paginate(10);
        }

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

        $products = Product::where('stock', '>', 0)
        ->whereHas('skinTypes', function ($q) use ($skinTypeId) {
            $q->where('skin_types.id', $skinTypeId);
        })
        ->withCount('reviews')
        ->withAvg('reviews', 'rating')
        ->with(['brand', 'subCategory', 'category'])
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

        $products = Product::where('stock', '>', 0)
        ->whereHas('goals', function ($q) use ($goalId) {
            $q->where('goal_id', $goalId);
        })
        ->withCount('reviews')
        ->withAvg('reviews', 'rating')
        ->with(['brand', 'subCategory', 'category'])
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

            // Retrieve original product concerns, skin types, and goals
            $skinTypeIds = $product->skinTypes->pluck('id')->toArray();
            $concernIds = $product->concerns->pluck('id')->toArray();
            $goalIds = $product->goals->pluck('id')->toArray();

            // Find other products belonging to the same step
            $otherProducts = Product::where('id', '!=', $product->id)
                ->whereHas('routines', function ($q) use ($stepId) {
                    $q->where('routine_step_id', $stepId);
                })
                ->with(['skinTypes', 'concerns', 'goals'])
                ->get();

            // Calculate relevance score and sort
            $otherProducts = $otherProducts->map(function ($altProd) use ($skinTypeIds, $concernIds, $goalIds) {
                $altSkinTypes = $altProd->skinTypes->pluck('id')->toArray();
                $altConcerns = $altProd->concerns->pluck('id')->toArray();
                $altGoals = $altProd->goals->pluck('id')->toArray();

                $sharedConcerns = count(array_intersect($concernIds, $altConcerns));
                $sharedSkinTypes = count(array_intersect($skinTypeIds, $altSkinTypes));
                $sharedGoals = count(array_intersect($goalIds, $altGoals));

                // Score calculation: prioritize concerns (problems), then goals, then skin types
                $score = ($sharedConcerns * 10) + ($sharedGoals * 5) + ($sharedSkinTypes * 3);

                // Small sales count weight to favor popular products among equal matches
                $score += min(($altProd->sales_count ?? 0) / 1000, 0.99);

                $altProd->relevance_score = $score;
                return $altProd;
            })->sortByDesc('relevance_score')->values();

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
