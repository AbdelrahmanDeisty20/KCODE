<?php

namespace App\Http\Resources\API\PRODUCT;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductRecommendationRuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if (!$this->resource) {
            return [];
        }

        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'default_priority_score' => $this->default_priority_score,
            'same_step_choice_group' => $this->same_step_choice_group,
            'am_default' => (bool)$this->am_default,
            'pm_default' => (bool)$this->pm_default,
            'selection_rule_ar' => $this->selection_rule_ar,
            'max_default_products_per_step' => $this->max_default_products_per_step,
            'selection_weight_formula_note' => $this->selection_weight_formula_note,
            'selection_priority_tie_breaker' => $this->selection_priority_tie_breaker,
            'exclusion_rule' => $this->exclusion_rule,
            'conflict_rule_strictness' => $this->conflict_rule_strictness,
            'pairing_rule' => $this->pairing_rule,
            'alternative_button_rule' => $this->alternative_button_rule,
            'add_on_display_rule' => $this->add_on_display_rule,
            'routine_builder_note' => $this->routine_builder_note,
            'fallback_product_rule' => $this->fallback_product_rule,
            'routine_role' => $this->routine_role,
        ];
    }
}
