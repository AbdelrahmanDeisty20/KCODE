<?php

namespace App\Http\Resources\API\PRODUCT;

use App\Http\Resources\API\BRAND\BrandResource;
use App\Http\Resources\API\CATEGORY\CategoryResource;
use App\Http\Resources\API\CATEGORY\SubCategoryResource;
use App\Http\Resources\API\Offer\OfferResource;
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
            'offers' => OfferResource::collection($this->whenLoaded('offers')),
            'is_favorite' => auth('sanctum')->check() ? $this->favorites()->where('user_id', auth('sanctum')->id())->where('is_active', true)->exists() : false,
            'sales_count' => (int) $this->sales_count,
            'review_rating' => $this->average_rating,
            'num_reviews' => $this->num_reviews,
            'category' => CategoryResource::make($this->whenLoaded('category')),
        ];
    }
}
