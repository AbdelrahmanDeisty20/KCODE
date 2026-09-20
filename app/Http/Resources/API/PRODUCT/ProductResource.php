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
        return [
            'id' => $this->id,
            'name' => $this->name_en,
            'description' => $this->description,
            'sku' => $this->sku,
            'price' => $this->price,
            'stock' => $this->stock,
            'image' => $this->image_path,
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
            'size' => $this->size,
            'barcode' => $this->barcode,
            'sensitive_eligible' => (bool) $this->sensitive_eligible,
            'brief_insight_ar' => $this->brief_insight_ar,
            'country_of_origin_ar' => $this->country_of_origin_ar,
            'role_ar' => $this->role_ar,
            'limitations_notes_ar' => $this->limitations_notes_ar,
            'texture' => $this->texture,
            'why_kcode' => $this->why_kcode,
            'usage_frequency_ar' => $this->usage_frequency_ar,
            'active_strength_level' => $this->active_strength_level,
            'safety_notes' => $this->safety_notes,
            'key_benefits' => $this->key_benefits,
            // PDP Structured Sections (New)
            'routine_position' => $this->getRoutinePosition(),
            'usage_instructions' => $this->getUsageInstructions(),
            'complementary_routine' => $this->getComplementaryRoutine(),
            'product_faqs' => $this->getProductFaqs(),

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
            'offers' => OfferResource::collection($this->whenLoaded('offers')),
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'product_images' => ProductImagesResource::collection($this->whenLoaded('images')),
        ];
    }

    /**
     * Get 5-step routine position for current product dynamically from DB
     */
    protected function getRoutinePosition(): ?array
    {
        $dbSteps = $this->routine_steps_json;

        if (empty($dbSteps) || !is_array($dbSteps)) {
            return null;
        }

        $stepNumber = $this->routine_step_number ?: $this->detectRoutineStepNumber();

        $formattedSteps = array_map(function ($s) use ($stepNumber) {
            $isCurrent = (int) ($s['step'] ?? 0) === (int) $stepNumber;
            return [
                'step' => (int) ($s['step'] ?? 1),
                'title' => $s['title'] ?? '',
                'subtitle' => $isCurrent ? ($s['subtitle'] ?? 'هذا المنتج') : ($s['subtitle'] ?? null),
                'is_current' => $isCurrent,
            ];
        }, $dbSteps);

        usort($formattedSteps, fn ($a, $b) => $a['step'] <=> $b['step']);

        return [
            'step_number' => (int) $stepNumber,
            'total_steps' => count($formattedSteps),
            'step_title' => $this->routine_step_title_ar ?: ($this->category?->name_ar ?? 'الروتين'),
            'steps' => $formattedSteps,
        ];
    }

    /**
     * Detect step number dynamically based on database category
     */
    protected function detectRoutineStepNumber(): int
    {
        if ($this->category_id) {
            $step = match ((int) $this->category_id) {
                1 => 1, // غسول
                2, 3 => 2, // تونر وإسنس
                4 => 3, // سيروم وأمبول
                5 => 4, // مرطب
                6 => 5, // واقي شمس
                default => 3,
            };
            if ($step) {
                return $step;
            }
        }

        $dbRoutineStep = $this->routines()->first()?->routineStep;
        if ($dbRoutineStep) {
            $stepName = strtolower($dbRoutineStep->name_en);
            if (str_contains($stepName, 'cleans')) return 1;
            if (str_contains($stepName, 'toner') || str_contains($stepName, 'essence')) return 2;
            if (str_contains($stepName, 'serum') || str_contains($stepName, 'ampoule')) return 3;
            if (str_contains($stepName, 'moistur') || str_contains($stepName, 'cream')) return 4;
            if (str_contains($stepName, 'sun')) return 5;
        }

        return 3;
    }

    /**
     * Get usage instructions from DB column
     */
    protected function getUsageInstructions(): ?array
    {
        if (!empty($this->usage_instructions_json) && is_array($this->usage_instructions_json)) {
            return array_merge($this->usage_instructions_json, [
                'raw_how_to_use' => $this->how_to_use,
            ]);
        }

        return null;
    }

    /**
     * Get complementary products for completing routine from DB column with brand/category smart fallback
     */
    protected function getComplementaryRoutine(): array
    {
        $items = $this->complementary_routine_json;
        $result = [];

        if (!empty($items) && is_array($items)) {
            foreach ($items as $item) {
                $dbProduct = null;
                if (!empty($item['product_id'])) {
                    $dbProduct = \App\Models\Product::find($item['product_id']);
                } elseif (!empty($item['sku'])) {
                    $dbProduct = \App\Models\Product::where('sku', $item['sku'])->first();
                }

                if ($dbProduct && $dbProduct->id === $this->id) {
                    continue; // Do not recommend product to itself
                }

                if ($dbProduct) {
                    $result[] = [
                        'id' => $dbProduct->id,
                        'sku' => $dbProduct->sku,
                        'brand' => $dbProduct->brand?->name_en ?? ($item['brand'] ?? ''),
                        'name' => $dbProduct->name_en,
                        'size' => $dbProduct->size ?: ($item['size'] ?? ''),
                        'category' => $item['category'] ?? ($dbProduct->subCategory?->name_ar ?? 'منتج مكمل'),
                        'reason' => $item['reason'] ?? '',
                        'optional' => (bool) ($item['optional'] ?? false),
                        'price' => (float) $dbProduct->price,
                        'image' => $dbProduct->image_path,
                    ];
                } else {
                    $result[] = [
                        'id' => $item['product_id'] ?? null,
                        'sku' => $item['sku'] ?? '',
                        'brand' => $item['brand'] ?? '',
                        'name' => $item['name'] ?? '',
                        'size' => $item['size'] ?? '',
                        'category' => $item['category'] ?? '',
                        'reason' => $item['reason'] ?? '',
                        'optional' => (bool) ($item['optional'] ?? false),
                        'price' => (float) ($item['price'] ?? 0),
                        'image' => $item['image'] ?? null,
                    ];
                }
            }
        }

        if (!empty($result)) {
            return $result;
        }

        // Smart Brand & Category Fallback: Generate complementary routine from same brand or active products
        $stepsToFetch = [
            ['cat_ids' => [1], 'keyword' => 'cleans', 'label' => 'غسول', 'reason' => 'تنظيف لطيف قبل استخدام المنتج', 'optional' => false],
            ['cat_ids' => [2, 3], 'keyword' => 'toner', 'label' => 'تونر', 'reason' => 'ترطيب خفيف وتهيئة البشرة', 'optional' => true],
            ['cat_ids' => [5], 'keyword' => 'moistur', 'label' => 'مرطب', 'reason' => 'حبس الرطوبة وتدعيم الحاجز الجلدي', 'optional' => false],
            ['cat_ids' => [6], 'keyword' => 'sun', 'label' => 'واقي شمس', 'reason' => 'حماية صباحية لإكمال الروتين', 'optional' => false],
        ];

        // If current product is one of these steps (e.g. cleanser), replace that step with serum (cat_id 4)
        $currentStep = $this->detectRoutineStepNumber();
        if ($currentStep === 1) {
            $stepsToFetch[0] = ['cat_ids' => [4], 'keyword' => 'serum', 'label' => 'سيروم', 'reason' => 'علاج مركز بعد التنظيف', 'optional' => false];
        } elseif ($currentStep === 2) {
            $stepsToFetch[1] = ['cat_ids' => [4], 'keyword' => 'serum', 'label' => 'سيروم', 'reason' => 'علاج مركز بعد التونر', 'optional' => false];
        } elseif ($currentStep === 4) {
            $stepsToFetch[2] = ['cat_ids' => [4], 'keyword' => 'serum', 'label' => 'سيروم', 'reason' => 'علاج مركز قبل المرطب', 'optional' => false];
        } elseif ($currentStep === 5) {
            $stepsToFetch[3] = ['cat_ids' => [4], 'keyword' => 'serum', 'label' => 'سيروم', 'reason' => 'علاج مركز قبل واقي الشمس', 'optional' => false];
        }

        foreach ($stepsToFetch as $config) {
            $compProduct = null;
            if ($this->brand_id) {
                $compProduct = \App\Models\Product::where('id', '!=', $this->id)
                    ->where('brand_id', $this->brand_id)
                    ->where(function ($q) use ($config) {
                        $q->whereIn('category_id', $config['cat_ids'])
                          ->orWhereHas('subCategory', function ($sub) use ($config) {
                              $sub->where('name_en', 'LIKE', '%' . $config['keyword'] . '%');
                          });
                    })->first();
            }

            if (!$compProduct) {
                $compProduct = \App\Models\Product::where('id', '!=', $this->id)
                    ->where(function ($q) use ($config) {
                        $q->whereIn('category_id', $config['cat_ids'])
                          ->orWhereHas('subCategory', function ($sub) use ($config) {
                              $sub->where('name_en', 'LIKE', '%' . $config['keyword'] . '%');
                          });
                    })->first();
            }

            if ($compProduct) {
                $result[] = [
                    'id' => $compProduct->id,
                    'sku' => $compProduct->sku,
                    'brand' => $compProduct->brand?->name_en ?? '',
                    'name' => $compProduct->name_en,
                    'size' => $compProduct->size ?: '',
                    'category' => $config['label'],
                    'reason' => $config['reason'],
                    'optional' => $config['optional'],
                    'price' => (float) $compProduct->price,
                    'image' => $compProduct->image_path,
                ];
            }
        }

        return $result;
    }

    /**
     * Get product FAQs section from DB column
     */
    protected function getProductFaqs(): ?array
    {
        if (!empty($this->product_faqs_json) && is_array($this->product_faqs_json)) {
            return $this->product_faqs_json;
        }

        return null;
    }
}
