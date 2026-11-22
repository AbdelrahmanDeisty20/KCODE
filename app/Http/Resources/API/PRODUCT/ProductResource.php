<?php

namespace App\Http\Resources\API\PRODUCT;

use App\Http\Resources\API\BRAND\BrandResource;
use App\Http\Resources\API\CATEGORY\CategoryResource;
use App\Http\Resources\API\CATEGORY\SubCategoryResource;
use App\Http\Resources\API\Offer\OfferResource;
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
        $shortName = $this->fixedShortName();
        $name = $this->fixedFullName();
        $isFavorite = $this->isFavoritedByCurrentUser();
        $usageInstructions = $this->usage_instructions;
        $verdict = is_array($usageInstructions) ? ($usageInstructions['verdict'] ?? null) : null;
        $keyBenefits = $this->key_benefits;

        $galleryFilter = function () {
            $mainUrl = $this->image_path;
            $mainRaw = ltrim($this->image ?? '', '/');

            return $this->images
                ->filter(function ($img) use ($mainUrl, $mainRaw) {
                    $imgRaw = ltrim($img->images ?? '', '/');
                    if (str_contains($imgRaw, '_gallery_1.webp')) {
                        return false;
                    }
                    if (!empty($mainRaw) && $imgRaw === $mainRaw) {
                        return false;
                    }

                    return $img->image_path && $img->image_path !== $mainUrl;
                })
                ->unique('image_path')
                ->values();
        };

        return [
            'id' => $this->id,
            'name' => $name,
            'name_en' => $this->name_en ?: $name,
            'name_ar' => $this->name_ar ?: $name,
            'description' => $this->description,
            'description_en' => $this->english_description,
            'short_description' => $this->brief_insight,
            'short_description_en' => $this->english_brief_insight,
            'sku' => $this->sku,
            'price' => $this->price,
            'stock' => $this->stock,
            'image' => $this->image_path,
            'status' => $this->status,
            'sales_count' => (int) $this->sales_count,
            'is_favorite' => $isFavorite,
            'short_name' => $shortName,
            'short_name_en' => $this->short_name_en ?: $shortName,
            'short_name_ar' => $this->short_name_ar ?: $shortName,
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
            'size' => $this->size,
            'barcode' => $this->barcode,
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
            'limitations_notes' => $this->limitations_notes,
            'limitations_notes_en' => $this->english_limitations_notes,
            'limitations_notes_ar' => $this->limitations_notes,
            'texture' => $this->texture,
            'texture_en' => $this->english_texture,
            'fragrance' => $this->fragrance,
            'fragrance_en' => $this->english_fragrance,
            'why_kcode' => $this->why_kcode,
            'usage_frequency' => $this->usage_frequency,
            'usage_frequency_en' => $this->english_usage_frequency,
            'usage_frequency_ar' => $this->usage_frequency,
            'active_strength_level' => $this->active_strength_level,
            'safety_notes' => $this->safety_notes,
            'key_benefits' => $keyBenefits,
            'formula_insights' => $keyBenefits ?: [],
            'routine_step' => $this->routine_step_number ?: $this->detectRoutineStepNumber(),
            'trust_badges' => $this->trust_badges,
            'promo_coupon' => $this->promo_coupon,
            'specifications' => $this->specifications,
            // Skin Types & Concerns Compatibility (using SkinType & Concern accessors)
            'skin_types' => $this->skinTypes->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'name_en' => $s->name_en,
            ]),
            'concerns' => $this->concerns->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'name_en' => $c->name_en,
            ]),
            // QuickView Summary (using Product & SkinType accessors)
            'quick_view' => [
                'role' => $this->role,
                'role_en' => $this->english_role,
                'best_for_skin_types' => $this->skinTypes->pluck('name')->filter()->values()->toArray(),
                'timing' => $this->usage_frequency,
                'texture' => $this->texture,
                'fragrance' => $this->fragrance,
                'actives' => $verdict['actives'] ?? ($this->primary_keyword ?: $shortName),
                'sensitive_eligible' => (bool) $this->sensitive_eligible,
                'sensitive_status' => $this->sensitive_status,
                'sensitive_guidance' => $this->sensitive_guidance,
                'note' => $this->brief_insight ?: $this->limitations_notes,
                'promo_coupon' => $this->promo_coupon,
                'trust_badges' => $this->trust_badges,
                'verdict' => $verdict,
                'summary_html' => $verdict['summary_html'] ?? null,
            ],
            // PDP Structured Sections (using Product accessors)
            'routine_position' => $this->routine_position,
            'usage_instructions' => $usageInstructions,
            'complementary_routine' => $this->complementary_routine,
            'product_faqs' => $this->product_faqs,
            'formula_evaluation_info' => $this->formula_evaluation_info,

            // SEO Fields
            'seo' => [
                'product_title_seo' => $this->product_title_seo,
                'en_short_hook' => $this->en_short_hook,
                'seo_meta_title' => $this->seo_meta_title,
                'seo_meta_title_ar' => $this->seo_meta_title,
                'seo_meta_title_en' => $this->english_seo_meta_title,
                'meta_description' => $this->meta_description,
                'primary_keyword' => $this->primary_keyword,
                'secondary_keywords' => $this->secondary_keywords,
                'final_url_slug' => $this->final_url_slug,
                'image_alt' => $this->image_alt,
                'og_title' => $this->og_title,
                'og_title_ar' => $this->og_title,
                'og_title_en' => $this->english_og_title,
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
            'offers' => OfferResource::collection($this->whenLoaded('offers')),
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'images' => ProductImagesResource::collection($this->whenLoaded('images', $galleryFilter)),
            'product_images' => ProductImagesResource::collection($this->whenLoaded('images', $galleryFilter)),
        ];
    }
}
