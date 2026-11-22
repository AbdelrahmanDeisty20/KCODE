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
        $shortName = $this->fixedShortName();
        $name = $this->fixedFullName();
        $isFavorite = $this->isFavoritedByCurrentUser();

        return [
            'id' => $this->id,
            'name' => $name,
            'sku' => $this->sku,
            'short_name' => $shortName,
            'name_en' => $this->name_en ?: $name,
            'name_ar' => $this->name_ar ?: $name,
            'short_name_en' => $this->short_name_en ?: $shortName,
            'short_name_ar' => $this->short_name_ar ?: $shortName,
            'description' => $this->description,
            'description_en' => $this->english_description,
            'short_description' => $this->brief_insight,
            'short_description_en' => $this->english_brief_insight,
            'price' => $this->price,
            'image' => $this->image_path,
            'brand' => BrandResource::make($this->whenLoaded('brand')),
            'sub_category' => SubCategoryResource::make($this->whenLoaded('subCategory')),
            'offers' => OfferResource::collection($this->whenLoaded('offers')),
            'is_favorite' => $isFavorite,
            'sales_count' => (int) $this->sales_count,
            'review_rating' => $this->average_rating,
            'num_reviews' => $this->num_reviews,
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'size' => $this->size,
            'barcode' => $this->barcode,
            'texture' => $this->texture,
            'texture_en' => $this->english_texture,
            'fragrance' => $this->fragrance,
            'fragrance_en' => $this->english_fragrance,
            'sensitive_eligible' => (bool) $this->sensitive_eligible,
            'sensitive_status' => $this->sensitive_status,
            'sensitive_status_en' => $this->english_sensitive_status,
            'sensitive_guidance' => $this->sensitive_guidance,
            'sensitive_guidance_en' => $this->english_sensitive_guidance,
            'brief_insight' => $this->brief_insight,
            'brief_insight_en' => $this->english_brief_insight,
            'brief_insight_ar' => $this->brief_insight,
            'country_of_origin' => $this->country_of_origin,
            'country_of_origin_en' => $this->english_country_of_origin,
            'country_of_origin_ar' => $this->country_of_origin,
            'role' => $this->role,
            'role_en' => $this->english_role,
            'role_ar' => $this->role,
        ];
    }
}
