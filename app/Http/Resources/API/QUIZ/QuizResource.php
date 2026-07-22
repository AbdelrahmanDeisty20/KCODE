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
        $data = is_array($this->resource) ? $this->resource : $this->resource->toArray();

        return [
            'id'       => $data['routine_id'] ?? null,
            'is_routine_added' => $data['is_routine_added'] ?? true,
            
            'diagnosis'        => $data['diagnosis'] ?? null,
            'questions'        => $data['questions'] ?? [],
            'routine'          => $data['routine'] ?? [],
        ];
    }
}
