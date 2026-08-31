<?php

namespace App\Http\Resources\API\ProductSelectionCriteria;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSelectionCriteriaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'title_ar'       => $this->title_ar,
            'title_en'       => $this->title_en,
            'description'    => $this->description,
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'icon'           => $this->icon,
            'badge_text'     => $this->badge_text,
            'badge_text_ar'  => $this->badge_text_ar,
            'badge_text_en'  => $this->badge_text_en,
            'type'           => $this->type,
            'sort_order'     => $this->sort_order,
            'is_active'      => $this->is_active,
        ];
    }
}
