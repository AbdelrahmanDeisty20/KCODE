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
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'icon'        => $this->icon,
            'badge_text'  => $this->badge_text,
            'type'        => $this->type,
            'sort_order'  => $this->sort_order,
            'is_active'   => $this->is_active,
        ];
    }
}
