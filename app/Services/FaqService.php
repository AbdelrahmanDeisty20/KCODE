<?php

namespace App\Services;

use App\Models\Faq;
use App\Support\StaticCache;

class FaqService
{
    /**
     * Get all active FAQs sorted (cached).
     */
    public function getActiveFaqs(): array
    {
        $faqs = StaticCache::remember('faqs', 'active', function () {
            return Faq::where('is_active', true)->orderBy('sort_order', 'asc')->get();
        });

        if ($faqs->isEmpty()) {
            return [
                'status'  => false,
                'message' => __('messages.faqs_not_found'),
                'data'    => [],
            ];
        }

        return [
            'status'  => true,
            'message' => __('messages.faqs_retrieved_successfully'),
            'data'    => $faqs,
        ];
    }
}
