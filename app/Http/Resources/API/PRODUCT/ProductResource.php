<?php

namespace App\Http\Resources\API\PRODUCT;

use App\Http\Resources\API\BRAND\BrandResource;
use App\Http\Resources\API\CATEGORY\CategoryResource;
use App\Http\Resources\API\CATEGORY\SubCategoryResource;
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
            'name_en' => $this->name_en,
            'name_ar' => $this->name_ar,
            'description' => $this->description,
            'description_en' => $this->description_en,
            'description_ar' => $this->description_ar,
            'sku' => $this->sku,
            'price' => $this->price,
            'stock' => $this->stock,
            'image' => $this->image,
            'status' => $this->status,
            'short_name' => $this->short_name,
            'short_name_en' => $this->short_name_en,
            'short_name_ar' => $this->short_name_ar,
            'ingredients' => $this->ingredients,
            'ingredients_en' => $this->ingredients_en,
            'ingredients_ar' => $this->ingredients_ar,
            'how_to_use' => $this->how_to_use,
            'how_to_use_en' => $this->how_to_use_en,
            'how_to_use_ar' => $this->how_to_use_ar,
            'review_rating' => $this->average_rating,
            'num_reviews' => $this->num_reviews,

            // Relations
            'brand' => BrandResource::make($this->whenLoaded('brand')),
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'sub_category' => SubCategoryResource::make($this->whenLoaded('subCategory')),

            // Product Details
            'texture' => $this->texture,
            'texture_ar' => $this->texture_ar,
            'texture_en' => $this->texture_en,
            'why_kcode' => $this->why_kcode,
            'why_kcode_ar' => $this->why_kcode_ar,
            'why_kcode_en' => $this->why_kcode_en,
            'usage_frequency_ar' => $this->usage_frequency_ar,
            'active_strength_level' => $this->active_strength_level,
            'safety_notes' => $this->safety_notes,
            'safety_notes_ar' => $this->safety_notes_ar,
            'safety_notes_en' => $this->safety_notes_en,
            'key_benefits' => $this->key_benefits,
            'ar_key_benefits' => $this->ar_key_benefits,
            'en_key_benefits' => $this->en_key_benefits,

            // SEO Fields
            'seo' => [
                'product_title_seo' => $this->product_title_seo,
                'ar_product_title_seo' => $this->ar_product_title_seo,
                'en_product_title_seo' => $this->en_product_title_seo,
                'en_short_hook' => $this->en_short_hook,
                'seo_meta_title_ar' => $this->seo_meta_title_ar,
                'meta_description' => $this->meta_description,
                'meta_description_en' => $this->meta_description_en,
                'meta_description_ar' => $this->meta_description_ar,
                'primary_keyword' => $this->primary_keyword,
                'primary_keyword_en' => $this->primary_keyword_en,
                'primary_keyword_ar' => $this->primary_keyword_ar,
                'secondary_keywords' => $this->secondary_keywords,
                'secondary_keywords_en' => $this->secondary_keywords_en,
                'secondary_keywords_ar' => $this->secondary_keywords_ar,
                'final_url_slug' => $this->final_url_slug,
                'image_alt' => $this->image_alt,
                'image_alt_en' => $this->image_alt_en,
                'image_alt_ar' => $this->image_alt_ar,
                'og_title_ar' => $this->og_title_ar,
                'og_description' => $this->og_description,
                'og_description_en' => $this->og_description_en,
                'og_description_ar' => $this->og_description_ar,
                'pdp_headline_en' => $this->pdp_headline_en,
                'above_fold_hook_en' => $this->above_fold_hook_en,
                'keywords' => $this->keywords,
            ]
        ];
    }
}
