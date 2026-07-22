<?php

namespace App\Http\Resources\API\QUIZ;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\API\PRODUCT\ProductListResource;

class FinalRoutineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lang = $request->header('lang') ?? app()->getLocale();

        $step = $this->relationLoaded('routineStep') ? $this->routineStep : null;
        $product = $this->relationLoaded('product') ? $this->product : null;

        // Retrieve product routine details
        $routineInfo = null;
        if ($product && $step) {
            $routineInfo = \App\Models\ProductRoutine::where('product_id', $product->id)
                ->where('routine_step_id', $step->id)
                ->first();
        }

        return [
            'routine_id' => $this->final_routine_id,
            'step_id' => $step ? $step->id : null,
            'step_name' => $step ? ($lang === 'ar' ? $step->name_ar : $step->name_en) : null,
            'step_order' => $this->temp_sequence_order ?? $this->step,
            'is_core' => $routineInfo ? (bool)$routineInfo->is_core : true,
            'is_addon' => $routineInfo ? (bool)$routineInfo->is_addon : false,
            'morning' => $routineInfo ? (bool)$routineInfo->morning : true,
            'night' => $routineInfo ? (bool)$routineInfo->night : true,
            'product' => $product ? new ProductListResource($product) : null,
        ];
    }
}
