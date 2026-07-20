<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyEarningRule extends Model
{
    protected $table = 'loyalty_earning_rules';

    protected $fillable = [
        'name',
        'target_type',
        'target_id',
        'multiplier',
        'fixed_points',
        'starts_at',
        'ends_at',
        'is_active',
    ];
}
