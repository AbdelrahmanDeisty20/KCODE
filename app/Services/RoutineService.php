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
                'status' => false,
                'message' => __('auth.unauthenticated'),
                'code' => 401
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
                'status' => false,
                'message' => __('messages.no_routine_found')
            ];
        }

        $finalRoutines->through(function ($finalRoutine) {
            $routineProducts = $finalRoutine->products->sortBy('step')->values();
            $routineProducts->each(function ($item, $index) {
                $item->temp_sequence_order = $index + 1;
            });

            return [
                'id' => $finalRoutine->routine_id ?? $finalRoutine->id,
                'items' => \App\Http\Resources\API\QUIZ\FinalRoutineResource::collection($routineProducts),
            ];
        });

        return [
            'status' => true,
            'message' => __('messages.routine_retrieved_successfully'),
            'data' => $finalRoutines,
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
                'status' => false,
                'message' => __('auth.unauthenticated'),
                'code' => 401
            ];
        }

        $assessment = Assessment::where('user_id', $user->id)->latest()->first();
        if (!$assessment) {
            return [
                'status' => false,
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
                'status' => false,
                'message' => __('messages.no_routine_found')
            ];
        }

        $routineProducts = $routine->routineProducts->sortBy('step')->values();

        $routineProducts->each(function ($item, $index) {
            $item->temp_sequence_order = $index + 1;
        });

        return [
            'status' => true,
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
                'status' => false,
                'message' => __('auth.unauthenticated'),
                'code' => 401
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
                'status' => false,
                'message' => __('messages.no_routine_found'),
                'code' => 404
            ];
        }

        // Create or update final routine record for this specific user AND routine_id
        $finalRoutine = \App\Models\FinalRoutine::firstOrCreate([
            'user_id' => $user->id,
            'routine_id' => $routine->id,
        ]);

        // Clear existing products for this final routine if re-saving
        $finalRoutine->products()->delete();

        // Copy active routine products to final routine products
        foreach ($routine->routineProducts as $rp) {
            $finalProductId = $rp->replaced_with_product_id ?: $rp->product_id;
            \App\Models\FinalRoutineProduct::create([
                'final_routine_id' => $finalRoutine->id,
                'product_id' => $finalProductId,
                'step' => $rp->step,
                'routine_step_id' => \App\Models\RoutineStep::where('order', $rp->step)->value('id'),
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

    /** Get preset routines directly from database with pagination. */

    /**
     * Get preset routines directly from database with pagination.
     */
    public function getPresetRoutines()
    {
        $lang = $this->resolveLocale();
        $perPage = (int) request()->get('per_page', 10);

        if (\App\Models\PresetRoutine::forWomen()->count() === 0 || request()->boolean('generate') || request()->boolean('refresh')) {
            (new \Database\Seeders\PresetRoutineSeeder())->run();
        }

        $query = \App\Models\PresetRoutine::forWomen()
            ->where('status', 'active')
            ->with(['items.product.brand']);

        if (request()->boolean('random')) {
            $query->inRandomOrder();
        }

        $paginated = $query->paginate($perPage);

        $responseRoutines = [];
        foreach ($paginated->items() as $routineModel) {
            $responseRoutines[] = $this->formatPresetRoutine($routineModel, $lang, false);
        }

        return [
            'status' => true,
            'message' => $lang === 'ar' ? 'تم جلب الروتينات المجهزة بنجاح.' : 'Preset routines retrieved successfully.',
            'data' => $responseRoutines,
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'last_page' => $paginated->lastPage(),
                'has_more_pages' => $paginated->hasMorePages(),
            ]
        ];
    }

    /**
     * Get single preset routine details by ID.
     */
    public function getPresetRoutineDetails($routineId = null)
    {
        $lang = $this->resolveLocale();
        $id = $routineId ?: request()->get('routine_id') ?: request()->get('id');

        $routineModel = \App\Models\PresetRoutine::where('status', 'active')
            ->where(function ($q) use ($id) {
                if (is_numeric($id)) {
                    $q->where('id', $id)->orWhere('slug', (string) $id);
                } else {
                    $q->where('slug', $id);
                }
            })
            ->with(['items.product.brand'])
            ->first();

        if (!$routineModel) {
            return [
                'status' => false,
                'code' => 404,
                'message' => $lang === 'ar' ? 'الروتين المجهّز غير موجود.' : 'Preset routine not found.',
                'data' => null
            ];
        }

        return [
            'status' => true,
            'message' => $lang === 'ar' ? 'تم جلب تفاصيل الروتين بنجاح.' : 'Routine details retrieved successfully.',
            'data' => $this->formatPresetRoutine($routineModel, $lang, true)
        ];
    }

    /**
     * Format a single PresetRoutine model.
     */
    private function formatPresetRoutine($routineModel, $lang, $includeItems = false)
    {
        $items = [];
        $totalPrice = 0;
        $previewImages = [];

        foreach ($routineModel->items as $itemModel) {
            $prod = $itemModel->product;
            if (!$prod)
                continue;

            $totalPrice += (float) $prod->price;
            $prodImage = $prod->image_path ?: $prod->image;
            if ($prodImage) {
                $previewImages[] = $prodImage;
            }

            if ($includeItems) {
                $brandImage = $prod->brand ? ($prod->brand->image_path ?: $prod->brand->image) : null;
                $useTimeValue = $this->localizeUseTime($itemModel, $lang);
                $items[] = [
                    'display_order' => (int) $itemModel->display_order,
                    'step_name' => $this->localizeStepName($itemModel, $prod, $lang),
                    'morning' => (bool) $itemModel->morning,
                    'night' => (bool) $itemModel->night,
                    'use_time' => $useTimeValue,
                    'use_time_ar' => $useTimeValue,
                    'reason' => $this->localizeItemReason($itemModel, $prod, $lang),
                    'frequency' => $this->localizeItemFrequency($itemModel, $prod, $lang),
                    'application' => $this->pickLocalized($lang, $itemModel->application_ar, $itemModel->application_en),
                    'starter_guide' => $this->pickLocalized($lang, $itemModel->starter_guide_ar, $itemModel->starter_guide_en),
                    'product' => [
                        'id' => $prod->id,
                        'name' => $prod->fixedFullName(),
                        'name_ar' => $prod->name_ar,
                        'short_name' => $prod->fixedShortName(),
                        'sku' => $prod->sku,
                        'price' => (float) $prod->price,
                        'size' => $prod->size,
                        'image' => $prodImage,
                        'average_rating' => (float) $prod->average_rating,
                        'num_reviews' => (int) $prod->num_reviews,
                        'brand' => $prod->brand ? [
                            'id' => $prod->brand->id,
                            'name' => $prod->brand->name_en,
                            'image' => $brandImage,
                        ] : null,
                    ]
                ];
            }
        }

        $titleLocalized = $this->pickLocalized($lang, $routineModel->title_ar, $routineModel->title_en, $routineModel->title_en ?: '');
        $defaultBadge = $lang === 'en'
            ? ($routineModel->is_for_men ? "Men's Care Routine" : 'Selected by KCODE Formulation Review')
            : ($routineModel->is_for_men ? 'عناية مخصصة للرجال' : 'مختارة وفق مراجعة KCODE للمركبات');

        $cover = $routineModel->cover;
        if (is_array($cover)) {
            $cover['alt'] = $lang === 'en'
                ? ($this->cleanEnglish($cover['alt_en'] ?? null) ?? ($titleLocalized ?: ''))
                : ($cover['alt'] ?? ($routineModel->title_ar ?? ''));
        }

        $res = [
            'id' => $routineModel->id,
            'routine_id' => $routineModel->id,
            'slug' => $routineModel->slug,
            'title' => $titleLocalized,
            'subtitle' => $this->pickLocalized($lang, $routineModel->subtitle_ar, $routineModel->subtitle_en),
            'description' => $this->pickLocalized($lang, $routineModel->description_ar, $routineModel->description_en),
            'badge' => $this->pickLocalized($lang, $routineModel->badge_ar, $routineModel->badge_en, $defaultBadge),
            'skin_type' => $this->pickLocalized($lang, $routineModel->skin_type_ar, $routineModel->skin_type_en),
            'goal' => $this->pickLocalized($lang, $routineModel->goal_ar, $routineModel->goal_en),
            'card_note' => $this->pickLocalized($lang, $routineModel->card_note_ar, $routineModel->card_note_en),
            'audience' => $this->pickLocalized($lang, $routineModel->audience_ar, $routineModel->audience_en),
            'timing' => $this->localizeTiming($routineModel->timing_json, $lang),
            'image' => $routineModel->cover_image_url,
            'cover_image' => $routineModel->cover_image_url,
            'cover' => $cover,
            'total_price' => round($totalPrice, 2),
            'products_count' => $routineModel->items->count(),
            'benefits' => $this->pickLocalizedArray($lang, $routineModel->benefits_json, $routineModel->benefits_en_json),
            'preview_images' => $previewImages,
        ];

        if ($includeItems) {
            $complementary = [];
            if (!empty($routineModel->complementary_products_json)) {
                foreach ($routineModel->complementary_products_json as $comp) {
                    $prodModel = null;
                    if (!empty($comp['product_id'])) {
                        $prodModel = \App\Models\Product::with('brand')->find($comp['product_id']);
                    } elseif (!empty($comp['sku'])) {
                        $prodModel = \App\Models\Product::with('brand')->where('sku', $comp['sku'])->first();
                    }
                    if ($prodModel) {
                        $whySelectedAr = $comp['why'] ?? $comp['reason'] ?? $comp['support_copy'] ?? ($prodModel->brief_insight_ar ?: $prodModel->why_kcode);
                        $whySelectedEn = $this->cleanEnglish(
                            $comp['why_en']
                                ?? $comp['reason_en']
                                ?? $comp['support_copy_en']
                                ?? $comp['why']
                                ?? $comp['reason']
                                ?? $comp['support_copy']
                                ?? null
                        ) ?? $this->cleanEnglish($prodModel->brief_insight_en ?? null);

                        $noteLocalized = $this->pickLocalized(
                            $lang,
                            $comp['note'] ?? null,
                            $comp['note_en'] ?? $comp['note'] ?? null
                        );

                        $stepLocalized = $lang === 'en'
                            ? ($this->cleanEnglish($comp['step_en'] ?? $comp['step'] ?? null)
                                ?? $this->cleanEnglish($prodModel->role_en ?? null)
                                ?? 'Complementary Product')
                            : ($comp['step'] ?? ($prodModel->role_ar ?: 'منتج مكمل'));

                        $compEntry = [
                            'product_id' => $prodModel->id,
                            'sku' => $prodModel->sku,
                            'name' => $prodModel->fixedFullName(),
                            'short_name' => $prodModel->fixedShortName(),
                            'name_ar' => $prodModel->name_ar,
                            'brand' => $prodModel->brand?->name_en,
                            'image' => $prodModel->image_path ?: $prodModel->image,
                            'price' => (float) $prodModel->price,
                            'size' => $prodModel->size,
                            'why_selected' => $lang === 'en' ? $whySelectedEn : $whySelectedAr,
                            'note' => $noteLocalized,
                            'step' => $stepLocalized,
                        ];

                        if (isset($comp['application']) || isset($comp['application_en'])) {
                            $compEntry['application'] = $this->pickLocalized(
                                $lang,
                                $comp['application'] ?? null,
                                $comp['application_en'] ?? $comp['application'] ?? null
                            );
                        }

                        $complementary[] = $compEntry;
                    } else {
                        $fallbackEntry = [
                            'product_id' => null,
                            'sku' => $comp['sku'] ?? null,
                            'name' => $this->cleanEnglish($comp['name'] ?? null) ?? ($comp['name'] ?? ''),
                            'name_ar' => $comp['name'] ?? '',
                            'why_selected' => $this->pickLocalized(
                                $lang,
                                $comp['why'] ?? $comp['reason'] ?? $comp['support_copy'] ?? null,
                                $comp['why_en'] ?? $comp['reason_en'] ?? $comp['support_copy_en'] ?? $comp['why'] ?? $comp['reason'] ?? null
                            ),
                            'note' => $this->pickLocalized(
                                $lang,
                                $comp['note'] ?? null,
                                $comp['note_en'] ?? $comp['note'] ?? null
                            ),
                            'step' => $lang === 'en'
                                ? ($this->cleanEnglish($comp['step_en'] ?? $comp['step'] ?? null) ?? 'Complementary Product')
                                : ($comp['step'] ?? 'منتج مكمل'),
                        ];
                        if (isset($comp['application']) || isset($comp['application_en'])) {
                            $fallbackEntry['application'] = $this->pickLocalized(
                                $lang,
                                $comp['application'] ?? null,
                                $comp['application_en'] ?? $comp['application'] ?? null
                            );
                        }
                        $complementary[] = $fallbackEntry;
                    }
                }
            }

            $usageGuide = $routineModel->usage_guide_json;
            if (empty($usageGuide) && !empty($routineModel->slug)) {
                static $staticRoutinesGuides = null;
                if ($staticRoutinesGuides === null) {
                    $staticRoutinesGuides = [];
                    $pPath = database_path('seeders/data/preset_routines_data.json');
                    if (file_exists($pPath)) {
                        foreach (json_decode(file_get_contents($pPath), true) ?: [] as $pr) {
                            if (!empty($pr['slug']) && !empty($pr['usage_guide_json'])) {
                                $staticRoutinesGuides[$pr['slug']] = $pr['usage_guide_json'];
                            }
                        }
                    }
                    $mPath = database_path('seeders/data/men_routines_data.json');
                    if (file_exists($mPath)) {
                        foreach (json_decode(file_get_contents($mPath), true) ?: [] as $mr) {
                            if (!empty($mr['slug']) && !empty($mr['usage_guide_json'])) {
                                $staticRoutinesGuides[$mr['slug']] = $mr['usage_guide_json'];
                            }
                        }
                    }
                }
                $usageGuide = $staticRoutinesGuides[$routineModel->slug] ?? null;
            }

            $res['what_it_offers'] = $this->pickLocalizedArray($lang, $routineModel->what_it_offers_json, $routineModel->what_it_offers_en_json);
            $res['how_to_use'] = $this->pickLocalized($lang, $routineModel->how_to_use_ar, $routineModel->how_to_use_en);
            $res['usage_guide'] = $this->localizeUsageGuide($usageGuide, $lang, $routineModel);
            $res['complementary_products'] = $complementary;
            $res['items'] = $items;
        }

        return $res;
    }

    /**
     * Resolve effective storefront language ('ar' or 'en').
     */
    private function resolveLocale(): string
    {
        $raw = request()->query('lang')
            ?? request()->header('lang')
            ?? request()->header('X-Language')
            ?? app()->getLocale()
            ?? 'ar';

        $normalized = strtolower(trim((string) $raw));
        return str_starts_with($normalized, 'en') ? 'en' : 'ar';
    }

    /**
     * Detect Arabic Unicode characters to prevent Arabic text leaking into English responses.
     */
    private function containsArabic(?string $text): bool
    {
        if ($text === null || $text === '') {
            return false;
        }
        return (bool) preg_match('/[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{08A0}-\x{08FF}]/u', $text);
    }

    /**
     * Return trimmed string only when non-empty and free of Arabic script.
     */
    private function cleanEnglish(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $trimmed = trim($value);
        if ($trimmed === '' || $this->containsArabic($trimmed)) {
            return null;
        }
        return $trimmed;
    }

    /**
     * Pick localized string for the active language with zero Arabic leakage in English mode.
     */
    private function pickLocalized(string $lang, ?string $arValue, ?string $enValue, ?string $enFallback = null): ?string
    {
        if ($lang === 'ar') {
            $trimmedAr = $arValue !== null ? trim($arValue) : '';
            if ($trimmedAr !== '') {
                return $trimmedAr;
            }
            $trimmedEn = $enValue !== null ? trim($enValue) : '';
            return $trimmedEn !== '' ? $trimmedEn : null;
        }

        return $this->cleanEnglish($enValue) ?? $this->cleanEnglish($enFallback);
    }

    /**
     * Pick localized string array (e.g., benefits / what_it_offers / notes).
     */
    private function pickLocalizedArray(string $lang, ?array $arItems, ?array $enItems): array
    {
        $arList = is_array($arItems) ? array_values(array_filter(array_map('strval', $arItems), fn (string $v) => trim($v) !== '')) : [];
        if ($lang === 'ar') {
            return $arList;
        }

        $enList = is_array($enItems)
            ? array_values(array_filter(array_map(fn ($v) => $this->cleanEnglish((string) $v), $enItems)))
            : [];

        if (!empty($enList)) {
            return $enList;
        }

        // Fallback only if legacy column stored English entries (never emit Arabic in EN mode).
        return array_values(array_filter(array_map(fn (string $v) => $this->cleanEnglish($v), $arList)));
    }

    private function localizeTiming(?array $timing, string $lang): ?array
    {
        if (!is_array($timing) || empty($timing)) {
            return null;
        }

        if ($lang === 'ar') {
            return [
                'am' => $timing['am'] ?? ($timing['am_en'] ?? null),
                'pm' => $timing['pm'] ?? ($timing['pm_en'] ?? null),
            ];
        }

        $amEn = $this->cleanEnglish($timing['am_en'] ?? null)
            ?? $this->cleanEnglish($timing['am'] ?? null);
        $pmEn = $this->cleanEnglish($timing['pm_en'] ?? null)
            ?? $this->cleanEnglish($timing['pm'] ?? null);

        return [
            'am' => $amEn,
            'pm' => $pmEn,
        ];
    }

    private function localizeUseTime($itemModel, string $lang): string
    {
        if ($lang === 'ar') {
            if (!empty($itemModel->use_time_ar)) {
                return $itemModel->use_time_ar;
            }
            if ($itemModel->morning && $itemModel->night) {
                return 'صباحاً ومساءً';
            }
            return $itemModel->morning ? 'صباحاً فقط' : 'مساءً فقط';
        }

        $explicitEn = $this->cleanEnglish($itemModel->use_time_en ?? null);
        if ($explicitEn !== null) {
            return $explicitEn;
        }

        $arNormalized = trim((string) ($itemModel->use_time_ar ?? ''));
        $map = [
            'صباحاً ومساءً' => 'Morning & Evening',
            'صباحًا ومساءً' => 'Morning & Evening',
            'صباحاً' => 'Morning',
            'صباحًا' => 'Morning',
            'صباحاً فقط' => 'Morning only',
            'صباحًا فقط' => 'Morning only',
            'مساءً' => 'Evening',
            'مساءً فقط' => 'Evening only',
        ];
        if (isset($map[$arNormalized])) {
            return $map[$arNormalized];
        }

        if ($itemModel->morning && $itemModel->night) {
            return 'Morning & Evening';
        }
        return $itemModel->morning ? 'Morning only' : 'Evening only';
    }

    private function localizeStepName($itemModel, $prod, string $lang): string
    {
        if ($lang === 'ar') {
            return $itemModel->step_name_ar ?: ('الخطوة ' . $itemModel->display_order);
        }

        return $this->cleanEnglish($itemModel->step_name_en ?? null)
            ?? $this->translateCommonRoleToEnglish($itemModel->step_name_ar ?? null)
            ?? $this->cleanEnglish($prod->role_en ?? null)
            ?? ('Step ' . $itemModel->display_order);
    }

    private function localizeItemReason($itemModel, $prod, string $lang): ?string
    {
        if ($lang === 'ar') {
            return $itemModel->reason_ar ?: ($prod->brief_insight_ar ?: $prod->why_kcode);
        }

        return $this->cleanEnglish($itemModel->reason_en ?? null)
            ?? $this->cleanEnglish($prod->brief_insight_en ?? null)
            ?? $this->cleanEnglish($prod->why_kcode_en ?? null);
    }

    private function localizeItemFrequency($itemModel, $prod, string $lang): string
    {
        if ($lang === 'ar') {
            return $itemModel->frequency_ar ?: ($prod->usage_frequency_ar ?: 'التكرار يومياً.');
        }

        return $this->cleanEnglish($itemModel->frequency_en ?? null)
            ?? $this->translateCommonFrequencyToEnglish($itemModel->frequency_ar ?? null)
            ?? $this->cleanEnglish($prod->usage_frequency_en ?? null)
            ?? 'Daily.';
    }

    private function translateCommonRoleToEnglish(?string $role): ?string
    {
        if ($role === null || trim($role) === '') {
            return null;
        }
        $normalized = trim($role);
        $clean = $this->cleanEnglish($normalized);
        if ($clean !== null) {
            return $clean;
        }

        $map = [
            'غسول' => 'Cleanser',
            'الغسول' => 'Cleanser',
            'التونر' => 'Toner',
            'تونر' => 'Toner',
            'الإسنس' => 'Essence',
            'الخلاصة المرطبة' => 'Essence',
            'السيروم' => 'Serum',
            'سيروم' => 'Serum',
            'المقشّر' => 'Exfoliator',
            'المقشر' => 'Exfoliator',
            'علاج خفيف' => 'Light Treatment',
            'بعد الحلاقة' => 'Post-Shave Care',
            'سيروم العين' => 'Eye Serum',
            'مرطب' => 'Moisturizer',
            'المرطب' => 'Moisturizer',
            'واقي شمس' => 'Sunscreen',
            'واقي الشمس' => 'Sunscreen',
        ];

        return $map[$normalized] ?? null;
    }

    private function translateCommonFrequencyToEnglish(?string $freq): ?string
    {
        if ($freq === null || trim($freq) === '') {
            return null;
        }
        $normalized = trim($freq);
        $clean = $this->cleanEnglish($normalized);
        if ($clean !== null) {
            return $clean;
        }

        $map = [
            'يوميًا.' => 'Daily.',
            'يومياً.' => 'Daily.',
            'يوميًا' => 'Daily',
            'يومياً' => 'Daily',
            'صباحًا.' => 'Morning.',
            'صباحاً.' => 'Morning.',
            'صباحًا ومساءً.' => 'Morning & evening.',
            'صباحاً ومساءً.' => 'Morning & evening.',
            'مساءً.' => 'Evening.',
            'بعد الحلاقة فقط.' => 'After shaving only.',
        ];

        return $map[$normalized] ?? null;
    }

    private function localizeUsageGuide(?array $usageGuide, string $lang, $routineModel): ?array
    {
        if (!is_array($usageGuide) || empty($usageGuide)) {
            return $usageGuide;
        }

        $productsBySku = $routineModel->items
            ->pluck('product')
            ->filter()
            ->keyBy('sku');

        if ($lang === 'ar') {
            return $usageGuide;
        }

        // Women routine usage_guide_json structure
        if (isset($usageGuide['am_steps']) || isset($usageGuide['pm_standard_steps']) || isset($usageGuide['pm_variants'])) {
            $result = $usageGuide;

            $result['am_title'] = $this->cleanEnglish($usageGuide['am_title_en'] ?? null) ?? 'Morning Steps';
            $result['pm_title'] = $this->cleanEnglish($usageGuide['pm_title_en'] ?? null) ?? 'Evening Steps';
            $result['pm_standard_label'] = $this->cleanEnglish($usageGuide['pm_standard_label_en'] ?? null) ?? 'Regular Evening Routine';
            $result['night_notes'] = $this->cleanEnglish($usageGuide['night_notes_en'] ?? null)
                ?? $this->cleanEnglish($usageGuide['night_notes'] ?? null)
                ?? $this->cleanEnglish($routineModel->card_note_en ?? null);

            if (!empty($usageGuide['am_steps']) && is_array($usageGuide['am_steps'])) {
                $result['am_steps'] = array_map(
                    fn ($step) => $this->localizeWomenUsageStep($step, $productsBySku),
                    $usageGuide['am_steps']
                );
            }

            if (!empty($usageGuide['pm_standard_steps']) && is_array($usageGuide['pm_standard_steps'])) {
                $result['pm_standard_steps'] = array_map(
                    fn ($step) => $this->localizeWomenUsageStep($step, $productsBySku),
                    $usageGuide['pm_standard_steps']
                );
            }

            if (!empty($usageGuide['pm_variants']) && is_array($usageGuide['pm_variants'])) {
                $result['pm_variants'] = array_map(function ($variant) use ($productsBySku) {
                    if (!is_array($variant)) {
                        return $variant;
                    }
                    $variant['label'] = $this->cleanEnglish($variant['label_en'] ?? null)
                        ?? $this->cleanEnglish($variant['label'] ?? null)
                        ?? 'Special Evening Routine';
                    $variant['badge'] = $this->cleanEnglish($variant['badge_en'] ?? null)
                        ?? $this->cleanEnglish($variant['badge'] ?? null);
                    $variant['note'] = $this->cleanEnglish($variant['note_en'] ?? null)
                        ?? $this->cleanEnglish($variant['note'] ?? null);
                    if (!empty($variant['steps']) && is_array($variant['steps'])) {
                        $variant['steps'] = array_map(
                            fn ($step) => $this->localizeWomenUsageStep($step, $productsBySku),
                            $variant['steps']
                        );
                    }
                    return $variant;
                }, $usageGuide['pm_variants']);
            }

            return $result;
        }

        // Men routine usage_guide_json structure
        $result = $usageGuide;

        if (!empty($usageGuide['morning_steps']) && is_array($usageGuide['morning_steps'])) {
            $result['morning_steps'] = array_map(
                fn ($step) => $this->localizeMenUsageStep($step, $productsBySku),
                $usageGuide['morning_steps']
            );
        }

        if (!empty($usageGuide['evening_variants']) && is_array($usageGuide['evening_variants'])) {
            $result['evening_variants'] = array_map(function ($variant) use ($productsBySku) {
                if (!is_array($variant)) {
                    return $variant;
                }
                $labelEn = $this->cleanEnglish($variant['label_en'] ?? null)
                    ?? $this->cleanEnglish($variant['label'] ?? null)
                    ?? ((($variant['id'] ?? null) === 'default') ? 'Regular Evenings' : 'Special Evenings');
                $variant['label'] = $labelEn;
                $variant['label_en'] = $labelEn;
                $variant['label_ar'] = $labelEn;

                if (!empty($variant['steps']) && is_array($variant['steps'])) {
                    $variant['steps'] = array_map(
                        fn ($step) => $this->localizeMenUsageStep($step, $productsBySku),
                        $variant['steps']
                    );
                }

                return $variant;
            }, $usageGuide['evening_variants']);
        }

        $notesEn = $this->pickLocalizedArray('en', $usageGuide['notes_ar'] ?? null, $usageGuide['notes_en'] ?? null);
        $result['notes'] = $notesEn;
        $result['notes_en'] = $notesEn;
        $result['notes_ar'] = $notesEn;

        return $result;
    }

    private function localizeWomenUsageStep(array $step, $productsBySku): array
    {
        $sku = $step['sku'] ?? null;
        $prod = $sku ? $productsBySku->get($sku) : null;

        $fixedName = $prod ? $prod->fixedFullName() : ($this->cleanEnglish($step['name'] ?? $step['name_ar'] ?? null) ?? '');
        if ($fixedName !== '') {
            $step['name'] = $fixedName;
            $step['name_ar'] = $fixedName;
        }

        $step['role'] = $this->cleanEnglish($step['role_en'] ?? null)
            ?? $this->translateCommonRoleToEnglish($step['role'] ?? null)
            ?? $this->cleanEnglish($prod?->role_en ?? null)
            ?? ('Step ' . ($step['step_number'] ?? ''));

        $step['application'] = $this->cleanEnglish($step['application_en'] ?? null)
            ?? $this->cleanEnglish($step['application'] ?? null)
            ?? $this->cleanEnglish($prod?->usage_frequency_en ?? null);

        $step['frequency'] = $this->cleanEnglish($step['frequency_en'] ?? null)
            ?? $this->translateCommonFrequencyToEnglish($step['frequency'] ?? null)
            ?? $this->cleanEnglish($prod?->usage_frequency_en ?? null)
            ?? 'Daily.';

        $starterEn = $this->cleanEnglish($step['starter_guide_en'] ?? null)
            ?? $this->cleanEnglish($step['starter_guide'] ?? null);
        $step['starter_guide'] = $starterEn ?? '';

        return $step;
    }

    private function localizeMenUsageStep(array $step, $productsBySku): array
    {
        $sku = $step['sku'] ?? null;
        $prod = $sku ? $productsBySku->get($sku) : null;

        $roleEn = $this->cleanEnglish($step['role_en'] ?? null)
            ?? $this->translateCommonRoleToEnglish($step['role_ar'] ?? null)
            ?? $this->cleanEnglish($prod?->role_en ?? null)
            ?? ('Step ' . ($step['step_number'] ?? ''));

        $instructionEn = $this->cleanEnglish($step['instruction_en'] ?? null)
            ?? $this->cleanEnglish($step['instruction'] ?? null)
            ?? $this->cleanEnglish($step['instruction_ar'] ?? null)
            ?? '';

        $step['role'] = $roleEn;
        $step['role_en'] = $roleEn;
        $step['role_ar'] = $roleEn;

        $step['instruction'] = $instructionEn;
        $step['instruction_en'] = $instructionEn;
        $step['instruction_ar'] = $instructionEn;

        if ($prod) {
            $step['product_name'] = $prod->fixedFullName();
        }

        return $step;
    }

    /**
     * Re-run preset routine seeder and return fresh routines.
     */
    public function generatePresetRoutines()
    {
        (new \Database\Seeders\PresetRoutineSeeder())->run();
        return $this->getPresetRoutines();
    }

    /**
     * Get Men's Simple Preset Routines directly from database with pagination.
     */
    public function getMenPresetRoutines()
    {
        $lang = $this->resolveLocale();
        $perPage = (int) request()->get('per_page', 10);

        if (\App\Models\PresetRoutine::forMen()->count() === 0 || request()->boolean('generate') || request()->boolean('refresh')) {
            (new \Database\Seeders\MenPresetRoutineSeeder())->run();
        }

        $query = \App\Models\PresetRoutine::forMen()
            ->where('status', 'active')
            ->with(['items.product.brand']);

        if (request()->boolean('random')) {
            $query->inRandomOrder();
        }

        $paginated = $query->paginate($perPage);

        $responseRoutines = [];
        foreach ($paginated->items() as $routineModel) {
            $responseRoutines[] = $this->formatPresetRoutine($routineModel, $lang, false);
        }

        return [
            'status' => true,
            'message' => $lang === 'ar' ? 'تم جلب روتينات الرجال المجهزة بنجاح.' : 'Men preset routines retrieved successfully.',
            'data' => $responseRoutines,
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'last_page' => $paginated->lastPage(),
                'has_more_pages' => $paginated->hasMorePages(),
            ]
        ];
    }

    /**
     * Generate Men's Simple Preset Routines using seeder.
     */
    public function generateMenPresetRoutines()
    {
        (new \Database\Seeders\MenPresetRoutineSeeder())->run();
        return $this->getMenPresetRoutines();
    }
}
