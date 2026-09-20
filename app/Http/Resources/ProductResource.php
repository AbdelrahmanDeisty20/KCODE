<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        $isAr = $locale === 'ar';

        return [
            'id'                  => (string) $this->id,
            'pageKey'             => $this->final_url_slug ?? 'anua-serum',
            'brand'               => $this->brand?->name ?? 'KCODE',
            'name'                => $this->name,
            'categoryLabel'       => $this->category?->name ?? '',
            'categories'          => array_filter([$this->category?->slug ?? 'serum']),
            'needs'               => $this->concerns ? $this->concerns->pluck('slug')->toArray() : [],
            'skins'               => $this->skinTypes ? $this->skinTypes->pluck('slug')->toArray() : [],
            'sensitiveEligible'   => (bool) $this->sensitive_eligible,
            'price'               => (float) $this->price,
            'size'                => $this->size ?? '30ml',
            'texture'             => $this->texture ?? '',
            'image'               => $this->image_path ?? asset('storage/products/default.jpg'),
            'inventory'           => (int) $this->stock,
            'rank'                => (int) ($this->sales_count ?? 1),
            'brief'               => $this->brief_insight_ar ?? $this->short_name_ar ?? $this->name,
            'description'         => $this->description,
            'actives'             => $this->ingredients,

            // PDP & QuickView structured payload matching frontend prototypes
            'quickView' => [
                'role'             => $this->role_ar ?? $this->why_kcode_ar ?? $this->name,
                'bestForSkinTypes' => $this->skinTypes ? $this->skinTypes->pluck('slug')->toArray() : [],
                'applicationZone'  => ['Face'],
                'timing'           => $this->usage_frequency_ar ?? 'صباحاً ومساءً',
                'note'             => $this->safety_notes_ar ? [
                    'label' => 'ملاحظات الأمان والاستخدام',
                    'text'  => $this->safety_notes_ar,
                ] : null,
                'approved' => [
                    'summaryHtml' => null,
                    'routine' => $this->routines && $this->routines->count() > 0 
                        ? $this->routines->map(fn($r) => [
                            'label'   => $r->step_name_ar ?? 'خطوة الروتين',
                            'current' => (bool) ($r->is_current ?? false),
                            'note'    => $r->note_ar ?? null,
                        ])->toArray()
                        : [
                            ['label' => 'غسول', 'current' => false, 'note' => null],
                            ['label' => 'سيروم', 'current' => true, 'note' => 'المنتج الحالي'],
                            ['label' => 'مرطب', 'current' => false, 'note' => null],
                            ['label' => 'واقي شمس', 'current' => false, 'note' => null],
                        ],
                    'usage' => [
                        [
                            'iconHtml' => null,
                            'label'    => 'توقيت الاستخدام',
                            'text'     => $this->usage_frequency_ar ?? 'صباحاً ومساءً',
                        ],
                        [
                            'iconHtml' => null,
                            'label'    => 'طريقة التطبيق',
                            'text'     => $this->how_to_use ?? 'ضع 2-3 قطرات على بشرة نظيفة قبل المرطب',
                        ],
                    ],
                ],
            ],

            // Technical dossier for PDP
            'dossier' => [
                'barcode'        => $this->barcode ?? $this->sku ?? '',
                'origin'         => $this->country_of_origin_ar ?? 'كوريا الجنوبية',
                'limitations'    => $this->limitations_notes_ar ?? $this->safety_notes_ar ?? '',
                'ingredients'    => $this->ingredients,
            ],
        ];
    }
}
