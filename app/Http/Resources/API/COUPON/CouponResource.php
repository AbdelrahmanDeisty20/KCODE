<?php

namespace App\Http\Resources\API\COUPON;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'code'                => $this->code,
            'title'               => $this->title,
            'discount_type'       => $this->discount_type,
            'discount_value'      => $this->discount_value,
            'min_order_amount'    => $this->min_order_amount,
            'max_discount_amount' => $this->max_discount_amount,
            'is_general'          => (bool) $this->is_general,
            'is_active'           => (bool) $this->is_active,
        ];
    }
}
