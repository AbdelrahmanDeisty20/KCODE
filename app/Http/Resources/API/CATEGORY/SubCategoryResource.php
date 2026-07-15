<?php

namespace App\Http\Resources\API\CATEGORY;

use App\Http\Resources\API\PRODUCT\ProductListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubCategoryResource extends JsonResource
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
            'name' => $this->name,
            'products_count' => (int)$this->products_count,
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'products' => ProductListResource::collection($this->whenLoaded('products')),
        ];
    }
}
