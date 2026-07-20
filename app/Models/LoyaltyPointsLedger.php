<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyPointsLedger extends Model
{
    protected $table = 'loyalty_points_ledger';

    protected $fillable = [
        'user_id',
        'points',
        'source_type',
        'source_id',
        'description_ar',
        'description_en',
        'expires_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
