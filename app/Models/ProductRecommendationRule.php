<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductRecommendationRule extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
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
        'is_catalog_eligible',
        'roles',
        'allowed_goals',
        'fit_oily',
        'fit_combination',
        'fit_normal',
        'fit_dry',
        'tolerance',
        'scores',
        'conflict_groups',
        'supplement_order',
        'supplement_kind',
        'supplement_goals',
        'exfoliator_purposes',
        'usage_policy',
        'usage_short_ar',
        'display_labels',
    ];

    protected $casts = [
        'am_default' => 'boolean',
        'pm_default' => 'boolean',
        'is_catalog_eligible' => 'boolean',
        'roles' => 'array',
        'allowed_goals' => 'array',
        'fit_oily' => 'float',
        'fit_combination' => 'float',
        'fit_normal' => 'float',
        'fit_dry' => 'float',
        'tolerance' => 'float',
        'scores' => 'array',
        'supplement_order' => 'integer',
        'supplement_goals' => 'array',
        'usage_policy' => 'array',
        'display_labels' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
