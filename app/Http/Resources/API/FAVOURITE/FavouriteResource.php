<?php

namespace App\Http\Resources\API\FAVOURITE;

use App\Http\Resources\API\PRODUCT\ProductListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavouriteResource extends JsonResource
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
            'user_id' => $this->user_id,
            'product_id' => $this->product_id,
            'is_active' => (bool) $this->is_active,
            'product' => new ProductListResource($this->whenLoaded('product')),
        ];
    }
}
