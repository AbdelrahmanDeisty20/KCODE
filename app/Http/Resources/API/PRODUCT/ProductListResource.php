<?php

namespace App\Http\Resources\API\PRODUCT;

use App\Http\Resources\API\BRAND\BrandResource;
use App\Http\Resources\API\CATEGORY\CategoryResource;
use App\Http\Resources\API\CATEGORY\SubCategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class ProductListResource extends JsonResource
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
            'sku' => $this->sku,
            'short_name' => $this->short_name,
            'price' => $this->price,
            'image' => $this->image,
            'brand' => BrandResource::make($this->whenLoaded('brand')),
            'sub_category' => SubCategoryResource::make($this->whenLoaded('subCategory')),
            'is_favorite' => (bool)$this->is_best_seller,
            'sales_count' => (int)$this->sales_count,
            'review_rating' => $this->average_rating,
            'num_reviews' => $this->num_reviews,
        ];
    }
}
