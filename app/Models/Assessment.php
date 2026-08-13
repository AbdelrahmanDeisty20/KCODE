<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $fillable = [
        'user_id',
        'skin_type_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skinType()
    {
        return $this->belongsTo(SkinType::class);
    }

    public function concerns()
    {
        return $this->hasMany(AssessmentConcern::class);
    }

    public function assessment_goals()
    {
        return $this->hasMany(AssessmentGoal::class);
    }

    public function routines()
    {
        return $this->hasMany(Routine::class);
    }

    public function answers()
    {
        return $this->hasMany(AssessmentAnswer::class);
    }

    /**
     * Get recommended products collection for this assessment.
     */
    public function getRecommendedProductsCollection()
    {
        $routineProductIds = [];
        foreach ($this->routines as $routine) {
            foreach ($routine->routineProducts as $rp) {
                if ($rp->product_id) {
                    $routineProductIds[] = $rp->product_id;
                }
            }
        }

        if (!empty($routineProductIds)) {
            return Product::whereIn('id', array_unique($routineProductIds))->get();
        }

        $concernIds = $this->concerns()->pluck('concern_id')->toArray();
        $skinTypeId = $this->skin_type_id;

        $query = Product::query();

        if ($skinTypeId) {
            $query->whereHas('skinTypes', fn ($q) => $q->where('skin_type_id', $skinTypeId));
        }

        if (!empty($concernIds)) {
            $query->orWhereHas('concerns', fn ($q) => $q->whereIn('concern_id', $concernIds));
        }

        return $query->take(10)->get();
    }
}
