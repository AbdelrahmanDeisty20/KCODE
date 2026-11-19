<?php

namespace App\Services;

use App\Models\CareGuide;
use App\Support\StaticCache;

class CareGuideService
{
    /**
     * Get active care guide with its items (cached).
     */
    public function getCareGuide(): array
    {
        $careGuide = StaticCache::remember('care_guide', 'active', function () {
            return CareGuide::with(['items' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order', 'asc');
            }])
            ->where('is_active', true)
            ->first();
        });

        if (!$careGuide) {
            return [
                'status'  => false,
                'message' => __('messages.care_guide_not_found'),
                'data'    => null,
            ];
        }

        return [
            'status'  => true,
            'message' => __('messages.care_guide_retrieved_successfully'),
            'data'    => $careGuide,
        ];
    }
}
