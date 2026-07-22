<?php

namespace App\Http\Resources\API\QUIZ;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'is_routine_added' => $this['is_routine_added'] ?? true,
            'routine_id'       => $this['routine_id'] ?? null,
            'diagnosis'        => $this['diagnosis'] ?? null,
            'questions'        => $this['questions'] ?? [],
            'routine'          => $this['routine'] ?? [],
        ];
    }
}
