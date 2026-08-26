<?php

namespace App\Http\Resources\API\QUIZ;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
{
    /**
     * Transform the resource into a 3-layer recommendation response using nested resources.
     */
    public function toArray(Request $request): array
    {
        $data = is_array($this->resource) ? $this->resource : [];

        return [
            'id'               => $data['routine_id'] ?? null,
            'routine_id'       => $data['routine_id'] ?? null,
            'is_routine_added' => $data['is_routine_added'] ?? true,
            'diagnosis'        => $data['diagnosis'] ?? null,
            'questions'        => $data['questions'] ?? [],
            'primary_routine'  => PrimaryRoutineResource::collection($data['primary_routine'] ?? []),
            'routine_support'  => RoutineSupportResource::collection($data['routine_support'] ?? []),
            'cart_addons'       => CartAddonResource::collection($data['cart_addons'] ?? []),
        ];
    }
}

