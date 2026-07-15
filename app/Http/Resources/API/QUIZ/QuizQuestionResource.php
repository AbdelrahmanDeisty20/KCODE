<?php

namespace App\Http\Resources\API\QUIZ;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizQuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'selection_type' => $this->selection_type,
            'step_number' => $this->step_number,
            'is_optional' => (bool)$this->is_optional,
            'options' => QuizOptionResource::collection($this->options),
        ];
    }
}
