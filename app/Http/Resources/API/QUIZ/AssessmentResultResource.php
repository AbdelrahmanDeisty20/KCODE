<?php

namespace App\Http\Resources\API\QUIZ;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssessmentResultResource extends JsonResource
{
    /**
     * Transform the assessment result into a 3-layer recommendation API response.
     */
    public function toArray(Request $request): array
    {
        return [
            'is_routine_added' => $this['is_routine_added'] ?? true,
            'routine_id'       => $this['routine_id'] ?? null,
            'diagnosis'        => $this['diagnosis'] ?? [],
            'questions'        => $this['questions'] ?? [],
            'primary_routine'  => PrimaryRoutineResource::collection($this['primary_routine'] ?? []),
            'routine_support'  => RoutineSupportResource::collection($this['routine_support'] ?? []),
            'cart_addons'       => CartAddonResource::collection($this['cart_addons'] ?? []),
        ];
    }
}
