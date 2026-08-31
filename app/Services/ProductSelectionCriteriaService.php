<?php

namespace App\Services;

use App\Models\ProductSelectionCriteria;

class ProductSelectionCriteriaService
{
    /**
     * Get active product selection criteria and methodology items from database.
     */
    public function getCriteria(): array
    {
        $allCriteria = ProductSelectionCriteria::active()
            ->orderBy('sort_order', 'asc')
            ->get();

        $modalCriteria = $allCriteria->where('type', 'modal_criteria')->values();
        $accordionItems = $allCriteria->where('type', 'accordion_item')->values();

        return [
            'status'  => true,
            'message' => __('messages.product_selection_criteria_retrieved_successfully'),
            'data'    => [
                'modal_criteria'  => $modalCriteria,
                'accordion_items' => $accordionItems,
            ],
        ];
    }
}
