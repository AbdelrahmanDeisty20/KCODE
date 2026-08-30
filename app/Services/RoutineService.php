<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\Routine;

class RoutineService
{
    /**
     * Get the authenticated user's routine products.
     */
    public function getUserRoutine(?int $routineId = null)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return [
                'status'  => false,
                'message' => __('auth.unauthenticated'),
                'code'    => 401
            ];
        }

        $query = \App\Models\FinalRoutine::where('user_id', $user->id)
            ->with([
                'products.routineStep',
                'products.product.brand',
                'products.product.routines'
            ]);

        if (!empty($routineId)) {
            $query->where(function ($q) use ($routineId) {
                $q->where('id', $routineId)->orWhere('routine_id', $routineId);
            });
        }

        $finalRoutines = $query->latest()->paginate(10);

        if ($finalRoutines->isEmpty()) {
            return [
                'status'  => false,
                'message' => __('messages.no_routine_found')
            ];
        }

        $finalRoutines->through(function ($finalRoutine) {
            $routineProducts = $finalRoutine->products->sortBy('step')->values();
            $routineProducts->each(function ($item, $index) {
                $item->temp_sequence_order = $index + 1;
            });

            return [
                'id'    => $finalRoutine->routine_id ?? $finalRoutine->id,
                'items' => \App\Http\Resources\API\QUIZ\FinalRoutineResource::collection($routineProducts),
            ];
        });

        return [
            'status'  => true,
            'message' => __('messages.routine_retrieved_successfully'),
            'data'    => $finalRoutines,
        ];
    }

    /**
     * Get the quiz-generated (suggested) routine — before user confirms.
     */
    public function getSuggestedRoutine()
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return [
                'status'  => false,
                'message' => __('auth.unauthenticated'),
                'code'    => 401
            ];
        }

        $assessment = Assessment::where('user_id', $user->id)->latest()->first();
        if (!$assessment) {
            return [
                'status'  => false,
                'message' => __('messages.no_routine_found')
            ];
        }

        $routine = Routine::where('assessment_id', $assessment->id)
            ->with([
                'routineProducts.routineStep',
                'routineProducts.product.brand',
                'routineProducts.product.routines',
                'routineProducts.replacedProduct.brand',
                'routineProducts.replacedProduct.routines',
            ])
            ->latest()
            ->first();

        if (!$routine) {
            return [
                'status'  => false,
                'message' => __('messages.no_routine_found')
            ];
        }

        $routineProducts = $routine->routineProducts->sortBy('step')->values();

        $routineProducts->each(function ($item, $index) {
            $item->temp_sequence_order = $index + 1;
        });

        return [
            'status'  => true,
            'message' => __('messages.routine_retrieved_successfully'),
            'data' => [
                'id' => $routine->id,
                'items' => $routineProducts,
            ]
        ];
    }

    /**
     * Save the routine to the user's personal account (finalized routine by routine_id).
     */
    public function saveFinalRoutine(int $routineId): array
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return [
                'status'  => false,
                'message' => __('auth.unauthenticated'),
                'code'    => 401
            ];
        }

        $routine = Routine::where('id', $routineId)
            ->whereHas('assessment', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with('routineProducts')
            ->first();

        if (!$routine) {
            return [
                'status'  => false,
                'message' => __('messages.no_routine_found'),
                'code'    => 404
            ];
        }

        // Create or update final routine record for this specific user AND routine_id
        $finalRoutine = \App\Models\FinalRoutine::firstOrCreate([
            'user_id'    => $user->id,
            'routine_id' => $routine->id,
        ]);

        // Clear existing products for this final routine if re-saving
        $finalRoutine->products()->delete();

        // Copy active routine products to final routine products
        foreach ($routine->routineProducts as $rp) {
            $finalProductId = $rp->replaced_with_product_id ?: $rp->product_id;
            \App\Models\FinalRoutineProduct::create([
                'final_routine_id' => $finalRoutine->id,
                'product_id'       => $finalProductId,
                'step'             => $rp->step,
                'routine_step_id'  => \App\Models\RoutineStep::where('order', $rp->step)->value('id'),
            ]);
        }

        // Clean up temporary products after confirmation
        $routine->routineProducts()->delete();

        return [
            'status' => true,
            'message' => __('messages.final_routine_saved_successfully')
        ];
    }

    /**
     * Replace a product in the routine with an alternative.
     */
    public function selectAlternativeProduct(int $stepId, int $alternativeProductId)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return [
                'status' => false,
                'message' => __('auth.unauthenticated'),
                'code' => 401
            ];
        }

        $assessment = Assessment::where('user_id', $user->id)->first();
        if (!$assessment) {
            return [
                'status' => false,
                'message' => __('messages.no_routine_found')
            ];
        }

        $routine = Routine::where('assessment_id', $assessment->id)->first();
        if (!$routine) {
            return [
                'status' => false,
                'message' => __('messages.no_routine_found')
            ];
        }

        $step = \App\Models\RoutineStep::find($stepId);
        if (!$step) {
            return [
                'status' => false,
                'message' => 'Routine step not found.'
            ];
        }

        // 1. Fetch current routine product for this step
        $routineProduct = \App\Models\RoutineProduct::where('routine_id', $routine->id)
            ->where('step', $step->order)
            ->first();

        if (!$routineProduct) {
            return [
                'status' => false,
                'message' => __('messages.no_routine_found')
            ];
        }

        $originalProductId = $routineProduct->product_id;

        // 2. Validate that alternativeProductId is in the valid alternatives list
        if ($alternativeProductId != $originalProductId) {
            $productService = new ProductService();
            $altResult = $productService->alternatives($originalProductId);

            $validAltIds = [];
            if ($altResult['status'] && isset($altResult['data'])) {
                $validAltIds = $altResult['data']->pluck('id')->toArray();
            }

            if (!in_array($alternativeProductId, $validAltIds)) {
                return [
                    'status' => false,
                    'message' => __('messages.invalid_alternative_product'),
                    'code' => 422
                ];
            }
        }

        // 3. Update in active quiz routine_products
        $routineProduct->update([
            'replaced_with_product_id' => $alternativeProductId
        ]);

        // 4. If user already finalized, update final_routine_products too
        $finalRoutine = \App\Models\FinalRoutine::where('user_id', $user->id)->first();
        if ($finalRoutine) {
            \App\Models\FinalRoutineProduct::updateOrCreate(
                [
                    'final_routine_id' => $finalRoutine->id,
                    'routine_step_id' => $step->id
                ],
                [
                    'product_id' => $alternativeProductId,
                    'step' => $step->order
                ]
            );
        }

        $product = \App\Models\Product::with(['brand', 'subCategory', 'offers'])->find($alternativeProductId);

        return [
            'status' => true,
            'message' => __('messages.alternative_selected_successfully'),
            'data' => $product
        ];
    }

    /**
     * Get finalized routine.
     */
    public function getFinalRoutine()
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return [
                'status' => false,
                'message' => __('auth.unauthenticated'),
                'code' => 401
            ];
        }

        $finalRoutine = \App\Models\FinalRoutine::where('user_id', $user->id)
            ->with([
                'products.routineStep',
                'products.product.brand'
            ])
            ->first();

        if (!$finalRoutine) {
            return [
                'status' => false,
                'message' => __('messages.final_routine_not_found'),
                'code' => 404
            ];
        }

        $sortedProducts = $finalRoutine->products->sortBy('step')->values();

        $sortedProducts->each(function ($item, $index) {
            $item->temp_sequence_order = $index + 1;
        });

        return [
            'status' => true,
            'message' => __('messages.routine_retrieved_successfully'),
            'data' => $sortedProducts
        ];
    }

    /**
     * Delete/reset a routine by routine_id (or user's active/final routine).
     */
    public function deleteRoutine(?int $routineId = null): array
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return [
                'status' => false,
                'message' => __('auth.unauthenticated'),
                'code' => 401
            ];
        }

        $deletedAny = false;

        // 1. Delete by explicit routine_id first if provided (must belong to $user)
        if ($routineId) {
            $finalRoutines = \App\Models\FinalRoutine::where('user_id', $user->id)
                ->where(function ($q) use ($routineId) {
                    $q
                        ->where('id', $routineId)
                        ->orWhere('routine_id', $routineId);
                })
                ->get();

            foreach ($finalRoutines as $fr) {
                $fr->products()->delete();
                $fr->delete();
                $deletedAny = true;
            }

            $routine = Routine::where('id', $routineId)
                ->whereHas('assessment', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->first();

            if ($routine) {
                \App\Models\RoutineProduct::where('routine_id', $routine->id)->delete();
                if ($routine->assessment_id) {
                    \App\Models\AssessmentGoal::where('assessment_id', $routine->assessment_id)->delete();
                    \App\Models\AssessmentConcern::where('assessment_id', $routine->assessment_id)->delete();
                    Assessment::where('id', $routine->assessment_id)->delete();
                }
                $routine->delete();
                $deletedAny = true;
            }
        } elseif ($user) {
            $finalRoutine = \App\Models\FinalRoutine::where('user_id', $user->id)->first();
            if ($finalRoutine) {
                $finalRoutine->products()->delete();
                $finalRoutine->delete();
                $deletedAny = true;
            }

            $assessment = Assessment::where('user_id', $user->id)->first();
            if ($assessment) {
                \App\Models\AssessmentGoal::where('assessment_id', $assessment->id)->delete();
                \App\Models\AssessmentConcern::where('assessment_id', $assessment->id)->delete();

                $routine = Routine::where('assessment_id', $assessment->id)->first();
                if ($routine) {
                    \App\Models\RoutineProduct::where('routine_id', $routine->id)->delete();
                    $routine->delete();
                }

                $assessment->delete();
                $deletedAny = true;
            }
        }

        if (!$deletedAny) {
            return [
                'status' => false,
                'message' => __('messages.no_routine_found'),
                'code' => 404
            ];
        }

        return [
            'status' => true,
            'message' => __('messages.routine_deleted_successfully')
        ];
    }

    /**
     * Remove a single product from the specified routine by routine_id.
     */
    public function removeProduct(int $productId, int $routineId): array
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return [
                'status' => false,
                'message' => __('auth.unauthenticated'),
                'code' => 401
            ];
        }

        // Verify routineId belongs to $user
        $userFinalRoutine = \App\Models\FinalRoutine::where('user_id', $user->id)
            ->where(function ($q) use ($routineId) {
                $q
                    ->where('id', $routineId)
                    ->orWhere('routine_id', $routineId);
            })
            ->first();

        $userQuizRoutine = Routine::where('id', $routineId)
            ->whereHas('assessment', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->first();

        if (!$userFinalRoutine && !$userQuizRoutine) {
            return [
                'status' => false,
                'message' => __('messages.no_routine_found'),
                'code' => 404
            ];
        }

        $deletedFinal = 0;
        if ($userFinalRoutine) {
            $deletedFinal = \App\Models\FinalRoutineProduct::where('final_routine_id', $userFinalRoutine->id)
                ->where('product_id', $productId)
                ->delete();
        }

        $deletedTemp = 0;
        if ($userQuizRoutine) {
            $deletedTemp = \App\Models\RoutineProduct::where('routine_id', $userQuizRoutine->id)
                ->where(function ($q) use ($productId) {
                    $q
                        ->where('product_id', $productId)
                        ->orWhere('replaced_with_product_id', $productId);
                })
                ->delete();
        }

        $deletedCount = $deletedFinal + $deletedTemp;

        if ($deletedCount === 0) {
            return [
                'status' => false,
                'message' => __('messages.product_not_found_in_routine'),
                'code' => 404
            ];
        }

        return [
            'status' => true,
            'message' => __('messages.product_removed_from_routine_successfully')
        ];
    }

    /**
     * Get 4 fixed preset routines automatically generated/fetched from database.
     */
    public function getPresetRoutines()
    {
        $lang = request()->header('lang') ?? app()->getLocale();

        $presetsConfig = [
            [
                'id' => 1,
                'title_ar' => 'روتين النضارة والتفتيح المضاعف',
                'title_en' => 'Ultimate Radiance & Brightening Routine',
                'description_ar' => 'روتين كوري متكامل يركز على تفتيح البقع الداكنة وتوحيد لون البشرة وإعطاء إشراقة زجاجية فورية.',
                'description_en' => 'A comprehensive Korean routine focused on fading dark spots, evening skin tone, and delivering a glass-skin glow.',
                'skin_type_ar' => 'جميع أنواع البشرة',
                'skin_type_en' => 'All Skin Types',
                'goal_ar' => 'تفتيح وإشراقة',
                'goal_en' => 'Brightening & Radiance',
                'badge_ar' => 'الأكثر مبيعاً ⭐',
                'badge_en' => 'Best Seller ⭐',
                'skin_type_id' => 3,
            ],
            [
                'id' => 2,
                'title_ar' => 'روتين تنقية المسام والسيطرة على الحبوب',
                'title_en' => 'Pore Purifying & Acne Control Routine',
                'description_ar' => 'مخصص للبشرة الدهنية والمختلطة لتنظيف المسام العميقة، السيطرة على الإفرازات الزيتية وتهدئة الحبوب.',
                'description_en' => 'Specially designed for oily and combination skin to deeply clear pores, control sebum, and soothe breakouts.',
                'skin_type_ar' => 'البشرة الدهنية والمختلطة',
                'skin_type_en' => 'Oily & Combination Skin',
                'goal_ar' => 'عناية بالمسام والحبوب',
                'goal_en' => 'Pore & Acne Care',
                'badge_ar' => 'موصى به صيدلانياً 🌿',
                'badge_en' => 'Dermatologist Recommended 🌿',
                'skin_type_id' => 1,
            ],
            [
                'id' => 3,
                'title_ar' => 'روتين الترميم الفائق وتدعيم حاجز البشرة',
                'title_en' => 'Barrier Repair & Intense Moisture Routine',
                'description_ar' => 'تركيبة غنية بالسيراميد والبانثينول لإصلاح حاجز البشرة المتضرر والحد من التقشر والجفاف الشديد.',
                'description_en' => 'Enriched with Ceramides and Panthenol to restore damaged skin barrier and relieve intense dryness.',
                'skin_type_ar' => 'البشرة الجافة والحساسة',
                'skin_type_en' => 'Dry & Sensitive Skin',
                'goal_ar' => 'ترميم وترطيب عميق',
                'goal_en' => 'Barrier Repair & Hydration',
                'badge_ar' => 'ترطيب مكثف 💧',
                'badge_en' => 'Intense Hydration 💧',
                'skin_type_id' => 2,
            ],
            [
                'id' => 4,
                'title_ar' => 'روتين الشاب النضر والكولاجين (Glass Skin)',
                'title_en' => 'Glass Skin & Youth Boosting Routine',
                'description_ar' => 'روتين يعتمد على الببتيدات والكولاجين لمنح البشرة ملمساً حريرياً ناعماً ونضارة شبابية دائمة.',
                'description_en' => 'Infused with Peptides and Collagen to firm the skin, refine texture, and achieve everlasting youthfulness.',
                'skin_type_ar' => 'البشرة العادية والمختلطة',
                'skin_type_en' => 'Normal & Combination Skin',
                'goal_ar' => 'نضارة ومقاومة التجاعيد',
                'goal_en' => 'Youth & Radiance',
                'badge_ar' => 'روتين مميز ✨',
                'badge_en' => 'Featured Routine ✨',
                'skin_type_id' => 3,
            ],
        ];

        $responseRoutines = [];

        foreach ($presetsConfig as $config) {
            $products = \App\Models\Product::where('stock', '>', 0)
                ->bestSeller()
                ->whereHas('skinTypes', function ($q) use ($config) {
                    $q->where('skin_type_id', $config['skin_type_id']);
                })
                ->inRandomOrder()
                ->with(['brand', 'routines'])
                ->take(5)
                ->get();

            if ($products->count() < 4) {
                $products = \App\Models\Product::where('stock', '>', 0)
                    ->inRandomOrder()
                    ->with(['brand', 'routines'])
                    ->take(4)
                    ->get();
            }

            $items = [];
            $totalPrice = 0;
            $order = 1;

            foreach ($products as $prod) {
                $totalPrice += $prod->price;
                $routineInfo = $prod->routines->first();

                $items[] = [
                    'display_order' => $order++,
                    'step_name' => $routineInfo ? ($lang === 'ar' ? $routineInfo->name_ar : $routineInfo->name_en) : ($lang === 'ar' ? "الخطوة {$order}" : "Step {$order}"),
                    'morning' => $routineInfo ? (bool)$routineInfo->morning : true,
                    'night' => $routineInfo ? (bool)$routineInfo->night : true,
                    'use_time_ar' => $lang === 'ar' ? 'صباحاً ومساءً' : 'Morning & Evening',
                    'product' => [
                        'id' => $prod->id,
                        'name' => $lang === 'ar' ? ($prod->display_ar_name ?: $prod->name) : ($prod->display_en_name ?: $prod->name),
                        'sku' => $prod->sku,
                        'price' => (float)$prod->price,
                        'image' => $prod->image_url,
                        'average_rating' => (float)$prod->average_rating,
                        'num_reviews' => (int)$prod->num_reviews,
                        'brand' => $prod->brand ? [
                            'id' => $prod->brand->id,
                            'name' => $lang === 'ar' ? $prod->brand->name_ar : $prod->brand->name_en,
                            'image' => $prod->brand->image_url ?? null,
                        ] : null,
                    ]
                ];
            }

            $responseRoutines[] = [
                'id' => $config['id'],
                'title' => $lang === 'ar' ? $config['title_ar'] : $config['title_en'],
                'description' => $lang === 'ar' ? $config['description_ar'] : $config['description_en'],
                'badge' => $lang === 'ar' ? $config['badge_ar'] : $config['badge_en'],
                'skin_type' => $lang === 'ar' ? $config['skin_type_ar'] : $config['skin_type_en'],
                'goal' => $lang === 'ar' ? $config['goal_ar'] : $config['goal_en'],
                'total_price' => round($totalPrice, 2),
                'products_count' => count($items),
                'items' => $items,
            ];
        }

        return [
            'status' => true,
            'message' => __('messages.preset_routines_retrieved_successfully') ?? 'Preset routines retrieved successfully.',
            'data' => $responseRoutines
        ];
    }
}
