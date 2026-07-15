<?php

namespace App\Http\Resources\API\PRODUCT;

use App\Http\Resources\API\BRAND\BrandResource;
use App\Http\Resources\API\CATEGORY\CategoryResource;
use App\Http\Resources\API\CATEGORY\SubCategoryResource;
use App\Http\Resources\API\Reviews\ReviewResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class ProductResource extends JsonResource
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
            'description' => $this->description,
            'sku' => $this->sku,
            'price' => $this->price,
            'stock' => $this->stock,
            'image' => $this->image,
            'status' => $this->status,
            'sales_count' => (int) $this->sales_count,
            'is_favorite' => auth('sanctum')->check() ? $this->favorites()->where('user_id', auth('sanctum')->id())->where('is_active', true)->exists() : false,
            'short_name' => $this->short_name,
            'ingredients' => $this->ingredients,
            'how_to_use' => $this->how_to_use,
            'review_rating' => $this->average_rating,
            'num_reviews' => $this->num_reviews,
            // reviews
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            // Relations
            'brand' => BrandResource::make($this->whenLoaded('brand')),
            'sub_category' => SubCategoryResource::make($this->whenLoaded('subCategory')),
            // Product Details
            'texture' => $this->texture,
            'why_kcode' => $this->why_kcode,
            'usage_frequency_ar' => $this->usage_frequency_ar,
            'active_strength_level' => $this->active_strength_level,
            'safety_notes' => $this->safety_notes,
            'key_benefits' => $this->key_benefits,
            // SEO Fields
            'seo' => [
                'product_title_seo' => $this->product_title_seo,
                'en_short_hook' => $this->en_short_hook,
                'seo_meta_title_ar' => $this->seo_meta_title_ar,
                'meta_description' => $this->meta_description,
                'primary_keyword' => $this->primary_keyword,
                'secondary_keywords' => $this->secondary_keywords,
                'final_url_slug' => $this->final_url_slug,
                'image_alt' => $this->image_alt,
                'og_title_ar' => $this->og_title_ar,
                'og_description' => $this->og_description,
                'pdp_headline_en' => $this->pdp_headline_en,
                'above_fold_hook_en' => $this->above_fold_hook_en,
                'keywords' => $this->keywords,
            ],
            // Marketing Details
            'marketing' => ProductMarketingDetailResource::make($this->whenLoaded('marketingDetail')),
            // Recommendation Rules
            'recommendation_rules' => ProductRecommendationRuleResource::make($this->whenLoaded('recommendationRule')),
            // Audit Details
            'audit' => ProductAuditResource::make($this->whenLoaded('audit')),
            'offers' => $this->whenLoaded('offers'),
        ];
    }
}
