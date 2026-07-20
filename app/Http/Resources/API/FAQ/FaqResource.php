<?php

namespace App\Http\Resources\API\FAQ;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'question'   => $this->question, // Accessor handles localization
            'answer'     => $this->answer,   // Accessor handles localization
            'sort_order' => $this->sort_order,
        ];
    }
}
