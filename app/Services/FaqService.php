<?php

namespace App\Services;

use App\Models\Faq;

class FaqService
{
    /**
     * Get all active FAQs sorted.
     */
    public function getActiveFaqs(): array
    {
        $faqs = Faq::where('is_active', true)->orderBy('sort_order', 'asc')->get();

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
