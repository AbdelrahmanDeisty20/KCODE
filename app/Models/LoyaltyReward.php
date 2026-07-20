<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyReward extends Model
{
    protected $table = 'loyalty_rewards';

    protected $fillable = [
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'points_required',
        'type',
        'value',
        'is_active',
    ];
}
