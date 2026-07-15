<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRecommendationRule extends Model
{
    protected $fillable = [
        'product_id',
        'default_priority_score',
        'same_step_choice_group',
        'am_default',
        'pm_default',
        'selection_rule_ar',
        'max_default_products_per_step',
        'selection_weight_formula_note',
        'selection_priority_tie_breaker',
        'exclusion_rule',
        'conflict_rule_strictness',
        'pairing_rule',
        'alternative_button_rule',
        'add_on_display_rule',
        'routine_builder_note',
        'fallback_product_rule',
        'routine_role',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
