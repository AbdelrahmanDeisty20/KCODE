<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductMarketingDetail extends Model
{
    protected $fillable = [
        'product_id',
        'primary_badge_en',
        'primary_badge_ar',
        'result_promise_en',
        'result_promise_ar',
        'objection_answer_en',
        'objection_answer_ar',
        'routine_reason_en',
        'routine_reason_ar',
        'bundle_cta_en',
        'bundle_cta_ar',
        'add_to_cart_microcopy_en',
        'add_to_cart_microcopy_ar',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getPrimaryBadgeAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->primary_badge_ar : $this->primary_badge_en;
    }

    public function getResultPromiseAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->result_promise_ar : $this->result_promise_en;
    }

    public function getObjectionAnswerAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->objection_answer_ar : $this->objection_answer_en;
    }

    public function getRoutineReasonAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->routine_reason_ar : $this->routine_reason_en;
    }

    public function getBundleCtaAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->bundle_cta_ar : $this->bundle_cta_en;
    }

    public function getAddToCartMicrocopyAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->add_to_cart_microcopy_ar : $this->add_to_cart_microcopy_en;
    }
}
