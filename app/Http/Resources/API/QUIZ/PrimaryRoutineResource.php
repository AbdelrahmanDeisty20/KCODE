<?php

namespace App\Http\Resources\API\QUIZ;

use App\Http\Resources\API\PRODUCT\ProductListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrimaryRoutineResource extends JsonResource
{
    /**
     * Transform the primary routine item into an array.
     */
    public function toArray(Request $request): array
    {
        $productData = is_array($this['product'] ?? null) ? $this['product'] : null;
        $productModel = is_object($this['product'] ?? null) ? $this['product'] : null;

        return [
            'display_order'       => $this['display_order'] ?? 1,
            'selected_by_default' => true,
            'step_id'             => $this['step_id'] ?? null,
            'routine_step_ar'     => $this['routine_step_ar'] ?? '',
            'routine_step_code'   => $this['routine_step_code'] ?? '',
            'use_time_ar'         => $this['use_time_ar'] ?? '',
            'usage_badge_ar'     => $this['usage_badge_ar'] ?? '',
            'chosen_for_ar'       => $this['chosen_for_ar'] ?? '',
            'product'             => $productModel ? new ProductListResource($productModel) : $productData,
        ];
    }
}
