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
            'key' => $this->key,
            'step' => $this->step_label,
            'step_label' => $this->step_label,
            'step_label_ar' => $this->step_label_ar,
            'step_label_en' => $this->step_label_en,
            'title' => $this->title,
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'description' => $this->description,
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'hint' => $this->description,
            'helper' => $this->helper,
            'helper_ar' => $this->helper_ar,
            'helper_en' => $this->helper_en,
            'selection_type' => $this->selection_type,
            'step_number' => $this->step_number,
            'is_optional' => (bool)$this->is_optional,
            'options' => QuizOptionResource::collection($this->options),
        ];
    }
}
