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
     * Save the routine to the user's personal account (finalized routine).
     */
    public function saveFinalRoutine()
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
            ->with('routineProducts')
            ->first();

        if (!$routine) {
            return [
                'status' => false,
                'message' => __('messages.no_routine_found')
            ];
        }

        // Create or update final routine record
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
                'product_id' => $finalProductId,
                'step' => $rp->step,
                'routine_step_id' => \App\Models\RoutineStep::where('order', $rp->step)->value('id'),
            ]);
        }

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

        // 1. Update in active quiz routine_products
        $routineProduct = \App\Models\RoutineProduct::where('routine_id', $routine->id)
            ->where('step', $step->order)
            ->first();

        if ($routineProduct) {
            $routineProduct->update([
                'replaced_with_product_id' => $alternativeProductId
            ]);
        }

        // 2. If user already finalized, update final_routine_products too
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
}
