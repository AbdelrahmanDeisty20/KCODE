<?php

namespace App\Http\Resources\API\PRODUCT;

use App\Http\Resources\API\BRAND\BrandResource;
use App\Http\Resources\API\CATEGORY\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'short_name' => app()->getLocale() == 'ar' ? $this->short_name_ar : $this->short_name_en,
            'price' => $this->price,
            'image' => $this->image,
            'brand' => BrandResource::make($this->whenLoaded('brand')),
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'review_rating' => $this->average_rating,
            'num_reviews' => $this->num_reviews,
        ];
    }
}
