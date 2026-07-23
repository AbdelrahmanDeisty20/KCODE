<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\Routine;

class RoutineService
{
    /**
     * Get the authenticated user's routine products.
     */
    public function getUserRoutine()
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
                'message' => __('messages.no_routine_found')
            ];
        }

        // Sort routine products by step order (ascending) and reset keys
        $routineProducts = $finalRoutine->products->sortBy('step')->values();

        // Assign sequential order starting from 1
        $routineProducts->each(function ($item, $index) {
            $item->temp_sequence_order = $index + 1;
        });

        return [
            'status' => true,
            'message' => __('messages.routine_retrieved_successfully'),
            'data' => $routineProducts
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

        $assessment = Assessment::where('user_id', $user->id)->first();
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
                'routineProducts.replacedProduct.brand',
            ])
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
            'data' => $routineProducts
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

        // Create or update final routine record for user
        $finalRoutine = \App\Models\FinalRoutine::updateOrCreate(
            ['user_id' => $user->id],
            ['routine_id' => $routine->id]
        );

        // Delete old final products if updating
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
            'status'  => true,
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
                'status'  => false,
                'message' => __('auth.unauthenticated'),
                'code'    => 401
            ];
        }

        $deletedAny = false;

        // 1. Delete by explicit routine_id first if provided (must belong to $user)
        if ($routineId) {
            $finalRoutines = \App\Models\FinalRoutine::where('user_id', $user->id)
                ->where(function ($q) use ($routineId) {
                    $q->where('id', $routineId)
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
                'status'  => false,
                'message' => __('messages.no_routine_found'),
                'code'    => 404
            ];
        }

        return [
            'status'  => true,
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
                'status'  => false,
                'message' => __('auth.unauthenticated'),
                'code'    => 401
            ];
        }

        // Verify routineId belongs to $user
        $userFinalRoutine = \App\Models\FinalRoutine::where('user_id', $user->id)
            ->where(function ($q) use ($routineId) {
                $q->where('id', $routineId)
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
                'status'  => false,
                'message' => __('messages.no_routine_found'),
                'code'    => 404
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
                    $q->where('product_id', $productId)
                      ->orWhere('replaced_with_product_id', $productId);
                })
                ->delete();
        }

        $deletedCount = $deletedFinal + $deletedTemp;

        if ($deletedCount === 0) {
            return [
                'status'  => false,
                'message' => __('messages.product_not_found_in_routine'),
                'code'    => 404
            ];
        }

        return [
            'status'  => true,
            'message' => __('messages.product_removed_from_routine_successfully')
        ];
    }
}
